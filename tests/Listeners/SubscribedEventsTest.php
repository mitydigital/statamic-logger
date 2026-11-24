<?php

use Illuminate\Auth\Events as Auth;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use MityDigital\StatamicLogger\Subscribers\StatamicLoggerEventSubscriber;
use Statamic\Entries\Collection;
use Statamic\Events;
use Statamic\Facades\Addon;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry;
use Statamic\Facades\Form;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Nav;
use Statamic\Facades\Role;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\Term;
use Statamic\Facades\User;
use Statamic\Facades\UserGroup;
use Statamic\Fields\Blueprint;
use Statamic\Fields\Fieldset;
use Statamic\Structures\CollectionStructure;

function makeSubscribedEvent(string $event): object
{
    $user = fn () => tap(User::make()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret'))->save();

    $container = fn () => tap(AssetContainer::make('test_container')->title('Test Container')->disk('assets'))->save();

    $asset = function (string $path = 'mity.png') use ($container) {
        $container()->disk()->put($path, file_get_contents(__DIR__.'/../__fixtures__/assets/mity.png'));

        return AssetContainer::find('test_container')->makeAsset($path);
    };

    $collection = fn () => tap((new Collection)->title('Blog Posts')->handle('blog')->sites(['default'])
        ->structure((new CollectionStructure)->maxDepth(1)))->save();

    $entry = fn () => tap(Entry::make()->collection($collection())->slug('entry')->data(['title' => 'My Entry']))->save();

    $taxonomy = fn () => tap(Taxonomy::make('my_taxonomy')->title('My Taxonomy'))->save();

    $term = function () use ($taxonomy) {
        $term = Term::make('my-term')->taxonomy($taxonomy());
        $term->in('default')->data(['title' => 'My Term']);

        return tap($term)->save();
    };

    $globals = fn () => tap(GlobalSet::make('settings')->title('Settings'))->save();

    $nav = fn () => tap(Nav::make('my_nav')->title('My Nav'))->save();

    $form = fn () => tap(Form::make('contact')->title('Contact'))->save();

    $blueprint = fn () => (new Blueprint)->setHandle('blog')->setNamespace('collections.blog')
        ->setContents(['title' => 'Blog Post', 'tabs' => []]);

    $fieldset = fn () => (new Fieldset)->setHandle('my_fieldset')->setContents(['title' => 'My Fieldset', 'fields' => []]);

    return match (true) {
        $event === Auth\Login::class => new $event('web', $user(), false),
        $event === Auth\Logout::class => new $event('web', $user()),
        $event === Auth\Failed::class => new $event('web', null, []),
        $event === Auth\PasswordReset::class => new $event($user()),

        $event === Events\AssetReplaced::class => new $event($asset('original.png'), $asset('new.png')),
        $event === Events\AssetReuploaded::class,
        $event === Events\AssetUploaded::class => new $event($asset(), 'mity.png'),
        $event === Events\SearchIndexUpdated::class => new $event('default'),
        $event === Events\TwoFactorRecoveryCodeReplaced::class => new $event($user(), 'code'),

        default => match (preg_replace('/^Statamic\\\\Events\\\\/', '', preg_replace('/(Saved|Created|Deleted|Creating|Saving|Reset)$/', '', $event))) {
            'AddonSettings' => new $event(Addon::get('mitydigital/statamic-logger')->settings()),
            'AssetContainer' => new $event($container()),
            'Asset', 'AssetReferencesUpdated' => new $event($asset()),
            'AssetFolder' => new $event($container()->assetFolder('new-folder')),
            'Blueprint' => new $event($blueprint()),
            'Collection' => new $event($collection()),
            'CollectionTree' => new $event($collection()->structure()->in('default')),
            'Entry', 'EntryScheduleReached' => new $event($entry()),
            'Fieldset' => new $event($fieldset()),
            'Form' => new $event($form()),
            'GlobalSet' => new $event($globals()),
            'GlobalVariables' => new $event($globals()->makeLocalization('default')),
            'ImpersonationStarted', 'ImpersonationEnded' => new $event($user(), $user()),
            'LocalizedTerm' => new $event($term()->in('default')),
            'Nav' => new $event($nav()),
            'NavTree' => new $event($nav()->makeTree('default', [])),
            'Revision' => new $event($entry()->makeRevision()),
            'Role' => new $event(Role::make('my_role')->title('My Role')),
            'Site' => new $event(Site::current()),
            'Submission', 'SubmissionFinalized' => new $event($form()->makeSubmission()->data(['name' => 'Peter'])),
            'Taxonomy' => new $event($taxonomy()),
            'Term', 'TermReferencesUpdated' => new $event($term()),
            'User', 'UserPasswordChanged', 'UserRegistered', 'ValidTwoFactorAuthenticationCodeProvided', 'TwoFactorAuthenticationChallenged',
            'TwoFactorAuthenticationDisabled', 'TwoFactorAuthenticationEnabled',
            'TwoFactorAuthenticationFailed' => new $event($user()),
            'UserGroup' => new $event(UserGroup::make('my_group')->title('My Group')),
            default => new $event,
        },
    };
}

it('logs and renders every subscribed event', function () {
    Event::fake();

    $failures = [];

    foreach ((new StatamicLoggerEventSubscriber)->subscribe(new Dispatcher) as $event => $listener) {
        try {
            $detail = renderLoggedEvent(new $listener, makeSubscribedEvent($event));

            // render errors, unmatched verbs and missing translations
            if (Str::contains($detail, [__('statamic-logger::errors.render'), class_basename($event), 'statamic-logger::'])) {
                $failures[$event] = $detail;
            }
        } catch (Throwable $exception) {
            $failures[$event] = $exception->getMessage().' at '.$exception->getFile().':'.$exception->getLine();
        }
    }

    expect($failures)->toBe([]);
});
