<?php
 /**
 * A collection of constants defining keys for registry- and session-entries.
 *
 */
interface Pandamp_Keys {

// -------- registry
    const REGISTRY_AUTH_OBJECT = 'com.pandamp.registry.authObject';

// -------- ext request object
    const EXT_REQUEST_OBJECT = 'com.pandamp.registry.extRequestObject';

// -------- app config in registry
    const REGISTRY_CONFIG_OBJECT = 'com.pandamp.registry.config';

// -------- application in registry
    const REGISTRY_APP_OBJECT = 'com.pandamp.registry.application';

// -------- session auth namespace
    const SESSION_AUTH_NAMESPACE = 'com.pandamp.session.authNamespace';

// -------- session reception controller
    const SESSION_CONTROLLER_RECEPTION = 'com.pandamp.session.receptionController';

// -------- cache db metadata
    const CACHE_DB_METADATA = 'com.pandamp.cache.db.metadata';

// -------- cache twitter accounts
    const CACHE_TWITTER_ACCOUNTS = 'com.pandamp.cache.twitter.accounts';
}