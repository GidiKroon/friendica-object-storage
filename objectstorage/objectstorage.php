<?php

/**
 * Name: Object Storage
 * Description: Add-on to use S3 object storage
 * Version: 0.1
 * Author: Gidi Kroon
 * Status: Work-in-progress
 */

use Friendica\Addon\objectstorage\ObjectStorageBackend;
use Friendica\Addon\objectstorage\ObjectStorageBackendConfig;
use Friendica\Core\Hook;
use Friendica\DI;

function objectstorage_install()
{
	Hook::register('storage_instance', __FILE__, 'objectstorage_storage_instance');
	Hook::register('storage_config', __FILE__, 'objectstorage_storage_config');
	DI::storageManager()->register(ObjectStorageBackend::class);
}

function objectstorage_uninstall()
{
	DI::storageManager()->unregister(ObjectStorageBackend::class);
}

function objectstorage_storage_instance($a, &$data)
{
	if ($data['name'] == ObjectStorageBackend::getName()) {
		$storageConfig = new ObjectStorageBackendConfig(DI::config(), DI::l10n());
		$data['storage'] = new ObjectStorageBackend($storageConfig);
	}
}

function objectstorage_storage_config($a, &$data)
{
	if ($data['name'] == ObjectStorageBackend::getName()) {
		$data['storage_config'] = new ObjectStorageBackendConfig(DI::config(), DI::l10n());
	}
}
