<?php

namespace MityDigital\StatamicLogger\Subscribers;

use Illuminate\Auth\Events\Failed as AuthFailed;
use Illuminate\Auth\Events\Login as AuthLogin;
use Illuminate\Auth\Events\Logout as AuthLogout;
use Illuminate\Auth\Events\PasswordReset as AuthPasswordReset;
use Illuminate\Events\Dispatcher;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\Listeners\Asset;
use MityDigital\StatamicLogger\Listeners\AssetContainer;
use MityDigital\StatamicLogger\Listeners\Blueprint;
use MityDigital\StatamicLogger\Listeners\Collection;
use MityDigital\StatamicLogger\Listeners\CollectionTree;
use MityDigital\StatamicLogger\Listeners\Entry;
use MityDigital\StatamicLogger\Listeners\Fieldset;
use MityDigital\StatamicLogger\Listeners\Form;
use MityDigital\StatamicLogger\Listeners\GlobalSet;
use MityDigital\StatamicLogger\Listeners\Impersonation;
use MityDigital\StatamicLogger\Listeners\Nav;
use MityDigital\StatamicLogger\Listeners\NavTree;
use MityDigital\StatamicLogger\Listeners\Role;
use MityDigital\StatamicLogger\Listeners\Site;
use MityDigital\StatamicLogger\Listeners\Taxonomy;
use MityDigital\StatamicLogger\Listeners\Term;
use MityDigital\StatamicLogger\Listeners\User;
use MityDigital\StatamicLogger\Listeners\UserGroup;
use MityDigital\StatamicLogger\Listeners\Utility;
use Statamic\Events\AssetContainerCreated;
use Statamic\Events\AssetContainerDeleted;
use Statamic\Events\AssetContainerSaved;
use Statamic\Events\AssetDeleted;
use Statamic\Events\AssetReplaced;
use Statamic\Events\AssetReuploaded;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetUploaded;
use Statamic\Events\BlueprintCreated;
use Statamic\Events\BlueprintDeleted;
use Statamic\Events\BlueprintSaved;
use Statamic\Events\CollectionCreated;
use Statamic\Events\CollectionDeleted;
use Statamic\Events\CollectionSaved;
use Statamic\Events\CollectionTreeDeleted;
use Statamic\Events\CollectionTreeSaved;
use Statamic\Events\EntryCreated;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;
use Statamic\Events\FieldsetCreated;
use Statamic\Events\FieldsetDeleted;
use Statamic\Events\FieldsetSaved;
use Statamic\Events\FormCreated;
use Statamic\Events\FormDeleted;
use Statamic\Events\FormSaved;
use Statamic\Events\GlideCacheCleared;
use Statamic\Events\GlobalSetCreated;
use Statamic\Events\GlobalSetDeleted;
use Statamic\Events\GlobalSetSaved;
use Statamic\Events\ImpersonationEnded;
use Statamic\Events\ImpersonationStarted;
use Statamic\Events\LicenseSet;
use Statamic\Events\LicensesRefreshed;
use Statamic\Events\NavDeleted;
use Statamic\Events\NavSaved;
use Statamic\Events\NavTreeDeleted;
use Statamic\Events\NavTreeSaved;
use Statamic\Events\RoleDeleted;
use Statamic\Events\RoleSaved;
use Statamic\Events\SearchIndexUpdated;
use Statamic\Events\SiteCreated;
use Statamic\Events\SiteDeleted;
use Statamic\Events\SiteSaved;
use Statamic\Events\StacheCleared;
use Statamic\Events\StacheWarmed;
use Statamic\Events\StaticCacheCleared;
use Statamic\Events\TaxonomyCreated;
use Statamic\Events\TaxonomyDeleted;
use Statamic\Events\TaxonomySaved;
use Statamic\Events\TermCreated;
use Statamic\Events\TermDeleted;
use Statamic\Events\TermSaved;
use Statamic\Events\UserCreated;
use Statamic\Events\UserDeleted;
use Statamic\Events\UserGroupDeleted;
use Statamic\Events\UserGroupSaved;
use Statamic\Events\UserPasswordChanged;
use Statamic\Events\UserSaved;

class StatamicLoggerEventSubscriber
{
    public function subscribe(Dispatcher $events): array
    {
        // if disabled, return an empty array
        if (! StatamicLogger::enabled()) {
            return [];
        }

        // get the default subscriptions
        $subscribed = [
            AssetContainerCreated::class => AssetContainer::class,
            AssetContainerDeleted::class => AssetContainer::class,
            AssetContainerSaved::class => AssetContainer::class,

            AssetDeleted::class => Asset::class,
            AssetReplaced::class => Asset::class,
            AssetReuploaded::class => Asset::class,
            AssetSaved::class => Asset::class,
            AssetUploaded::class => Asset::class,

            AuthLogin::class => User::class,
            AuthLogout::class => User::class,
            AuthFailed::class => User::class,
            AuthPasswordReset::class => User::class,

            BlueprintCreated::class => Blueprint::class,
            BlueprintDeleted::class => Blueprint::class,
            BlueprintSaved::class => Blueprint::class,

            CollectionCreated::class => Collection::class,
            CollectionDeleted::class => Collection::class,
            CollectionSaved::class => Collection::class,

            CollectionTreeDeleted::class => CollectionTree::class,
            CollectionTreeSaved::class => CollectionTree::class,

            EntryCreated::class => Entry::class,
            EntryDeleted::class => Entry::class,
            EntrySaved::class => Entry::class,

            FieldsetCreated::class => Fieldset::class,
            FieldsetDeleted::class => Fieldset::class,
            FieldsetSaved::class => Fieldset::class,

            FormCreated::class => Form::class,
            FormDeleted::class => Form::class,
            FormSaved::class => Form::class,

            GlideCacheCleared::class => Utility::class,

            GlobalSetCreated::class => GlobalSet::class,
            GlobalSetDeleted::class => GlobalSet::class,
            GlobalSetSaved::class => GlobalSet::class,

            ImpersonationEnded::class => Impersonation::class,
            ImpersonationStarted::class => Impersonation::class,

            LicenseSet::class => Utility::class,
            LicensesRefreshed::class => Utility::class,

            NavDeleted::class => Nav::class,
            NavSaved::class => Nav::class,

            NavTreeDeleted::class => NavTree::class,
            NavTreeSaved::class => NavTree::class,

            RoleDeleted::class => Role::class,
            RoleSaved::class => Role::class,

            SearchIndexUpdated::class => Utility::class,

            SiteCreated::class => Site::class,
            SiteDeleted::class => Site::class,
            SiteSaved::class => Site::class,

            StacheCleared::class => Utility::class,
            StacheWarmed::class => Utility::class,

            StaticCacheCleared::class => Utility::class,

            TaxonomyCreated::class => Taxonomy::class,
            TaxonomyDeleted::class => Taxonomy::class,
            TaxonomySaved::class => Taxonomy::class,

            TermCreated::class => Term::class,
            TermDeleted::class => Term::class,
            TermSaved::class => Term::class,

            UserCreated::class => User::class,
            UserDeleted::class => User::class,
            UserSaved::class => User::class,

            UserPasswordChanged::class => User::class,

            UserGroupDeleted::class => UserGroup::class,
            UserGroupSaved::class => UserGroup::class,
        ];

        // add additional includes
        foreach (StatamicLogger::getSubscribedEvents() as $event => $handler) {
            $subscribed[$event] = $handler;
        }

        // remove excludes
        foreach (config('statamic-logger.exclude', []) as $event) {
            if (array_key_exists($event, $subscribed)) {
                unset($subscribed[$event]);
            }
        }

        return $subscribed;
    }
}
