<?php

namespace Friendica\Addon\objectstorage;

use Exception;
use Friendica\Core\Storage\Exception\ReferenceStorageException;
use Friendica\Core\Storage\Exception\StorageException;
use Friendica\Core\Storage\Capability\ICanWriteToStorage;
use Friendica\Util\Strings;

class ObjectStorageBackend implements ICanWriteToStorage
{
	const NAME = 'Object Storage';

	/** @var ObjectStorageBackendConfig */
	private $storageConfig;

	/**
	 * ObjectStorageBackend constructor.
	 *
	 * @param ObjectStorageBackendConfig $storageConfig
	 */
	public function __construct(ObjectStorageBackendConfig $storageConfig)
	{
		$this->storageConfig = $storageConfig;
	}

	/**
	 * @inheritDoc
	 */
	public function get(string $reference): string
	{
		/// @TODO check reference, get contents
		throw new ReferenceStorageException(sprintf('%s storage failed to get the file %s, The file is invalid', self::NAME, $reference));
		$result = '';

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function put(string $data, string $reference = ''): string
	{
		if ($reference === '') {
			try {
				$reference = Strings::getRandomHex();
			} catch (Exception $exception) {
				throw new StorageException(sprintf('%s storage failed to generate a random hex', self::NAME), $exception->getCode(), $exception);
			}
		}

		/// @TODO store contents

		return $reference;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(string $reference)
	{
	}

	/**
	 * @inheritDoc
	 */
	public static function getName(): string
	{
		return self::NAME;
	}

	public function __toString(): string
	{
		return self::NAME;
	}
}
