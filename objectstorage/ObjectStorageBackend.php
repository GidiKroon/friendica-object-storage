<?php

namespace Friendica\Addon\objectstorage;

require_once __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Exception;
use Friendica\Core\Storage\Exception\ReferenceStorageException;
use Friendica\Core\Storage\Exception\StorageException;
use Friendica\Core\Storage\Capability\ICanWriteToStorage;
use Friendica\Util\Strings;

/**
 * Storage backend for object storage, e.g. in AWS S3.
 * 
 * Notes:
 * 
 * The S3 SDK supports using streams for the content to and from
 * the bucket, but the Friendica storage SPI doesn't. So
 * the full object data is in memory.
 * 
 * The Friendica storage SPI doesn't support setting a
 * custom url for stored objects, e.g. for using a cdn
 * or for direct linking to the bucket object. Friendica
 * will always read the object for you. This has the
 * nice consequence that the bucket doesn't need to be
 * public and that no public ACL or bucket policy needs
 * to be set.
 */
class ObjectStorageBackend implements ICanWriteToStorage
{
	const NAME = 'ObjectStorage';

	/** @var ObjectStorageBackendConfig */
	private $storageConfig;
	/** @var S3Client */
	private $s3Client;

	/**
	 * ObjectStorageBackend constructor.
	 *
	 * @param ObjectStorageBackendConfig $storageConfig
	 */
	public function __construct(ObjectStorageBackendConfig $storageConfig)
	{
		$this->storageConfig = $storageConfig;
		$this->s3Client = $this->createS3();
	}

	/**
	 * @inheritDoc
	 */
	public function get(string $reference): string
	{
		$params = [
			'Bucket' => $this->storageConfig->getBucket(),
			'Key' => $this->storageConfig->getPrefix() . $reference,
		];

		try {
			$result = $this->s3Client->headObject($params);
		} catch (S3Exception $exception) {
			throw new ReferenceStorageException(sprintf('%s storage failed to get the file %s, The file is invalid', self::NAME, $reference), $exception->getCode(), $exception);
		}

		try {
			$result = $this->s3Client->getObject($params);
		} catch (S3Exception $exception) {
			throw new StorageException(sprintf('%s storage failed to load the data', self::NAME), $exception->getCode(), $exception);
		}

		return $result['Body'];
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

		$params = [
			'ACL' => 'bucket-owner-full-control',
			'Body' => $data,
			'Bucket' => $this->storageConfig->getBucket(),
			'Key' => $this->storageConfig->getPrefix() . $reference,
		];

		try {
			$result = $this->s3Client->putObject($params);
		} catch (S3Exception $exception) {
			throw new StorageException(sprintf('%s storage failed to store the data', self::NAME), $exception->getCode(), $exception);
		}

		return $reference;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(string $reference)
	{
		$params = [
			'Bucket' => $this->storageConfig->getBucket(),
			'Key' => $this->storageConfig->getPrefix() . $reference,
		];

		try {
			$result = $this->s3Client->headObject($params);
		} catch (S3Exception $exception) {
			throw new ReferenceStorageException(sprintf('%s storage failed to delete the file %s, The file is invalid', self::NAME, $reference), $exception->getCode(), $exception);
		}

		try {
			$result = $this->s3Client->deleteObject($params);
		} catch (S3Exception $exception) {
			throw new StorageException(sprintf('%s storage failed to delete the data', self::NAME), $exception->getCode(), $exception);
		}
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

	private function createS3(): S3Client
	{
		$params = [
			'version' => '2006-03-01',
		];
		if ($this->storageConfig->getRegion() != '') {
			$params['region'] = $this->storageConfig->getRegion();
		}
		if ($this->storageConfig->getEndpoint() != '') {
			$params['endpoint'] = $this->storageConfig->getEndpoint();
		}

		$s3Client = new S3Client($params);
		return $s3Client;
	}
}
