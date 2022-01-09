<?php

namespace Friendica\Addon\objectstorage;

use Friendica\Core\Config\Capability\IManageConfigValues;
use Friendica\Core\L10n;
use Friendica\Core\Storage\Capability\ICanConfigureStorage;

/**
 * Configuration class for ObjectStorageBackend. It enables a
 * settings panel in the admin storage screens.
 * 
 * For AWS S3 one would normally set `objectstorage_region` and 
 * `objectstorage_bucket`. For non-AWS S3 one would typically 
 * set `objectstorage_endpoint` and `objectstorage_bucket`. The
 * bucket path prefix would be useful if you use the bucket
 * for other things as well, or if Friendica would allow different
 * storage configurations for different uses (photos, attachments,
 * avatars) which it currently doesn't.
 * 
 * The configuration does not include credentials. For AWS S3
 * access should be given securely with an EC2 instance
 * profile using an IAM role. Or credentials can be specified in
 * a `~/.aws/credentials` file or in enviroment variables.
 */
class ObjectStorageBackendConfig implements ICanConfigureStorage
{
	/** @var IManageConfigValues */
	private $config;
	/** @var L10n */
	private $l10n;
	/** @var string */
	private $region;
	/** @var string */
	private $endpoint;
	/** @var string */
	private $bucket;
	/** @var string */
	private $prefix;

	/**
	 * ObjectStorageBackendConfig constructor.
	 *
	 * @param IManageConfigValues $config
	 * @param L10n                $l10n
	 */
	public function __construct(IManageConfigValues $config, L10n $l10n)
	{
		$this->config = $config;
		$this->l10n   = $l10n;
		$this->region = $this->config->get('storage', 'objectstorage_region', '');
		$this->endpoint = $this->config->get('storage', 'objectstorage_endpoint', '');
		$this->bucket = $this->config->get('storage', 'objectstorage_bucket', '');
		$this->prefix = $this->config->get('storage', 'objectstorage_prefix', '');
	}

	/**
	 * @inheritDoc
	 */
	public function getOptions(): array
	{
		return [
			'region' => [
				'input',
				$this->l10n->t('The AWS region'),
				$this->region,
				$this->l10n->t('Enter the AWS region, e.g. "eu-west-1". This is required for Amazon AWS S3.'),
			],
			'endpoint' => [
				'input',
				$this->l10n->t('The S3 endpoint'),
				$this->endpoint,
				$this->l10n->t('Enter the S3 endpoint, e.g. "s3.eu-west-1.amazonaws.com". This is optional for Amazon AWS S3 but required for other S3 compatible services.'),
			],
			'bucket' => [
				'input',
				$this->l10n->t('The S3 bucket'),
				$this->bucket,
				$this->l10n->t('Enter the S3 bucket name, e.g. "my-bucket-example-org". This is required.'),
			],
			'prefix' => [
				'input',
				$this->l10n->t('The bucket path prefix'),
				$this->prefix,
				$this->l10n->t('Enter the path prefix to use for objects in the bucket, e.g. "photos/" or "friendica-". This is optional and defaults to "". It is useful if the bucket is used for other things as well.'),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function saveOptions(array $data): array
	{
		$region = $data['region'] ?? '';
		$this->config->set('storage', 'objectstorage_region', $region);
		$this->region = $region;

		$endpoint = $data['endpoint'] ?? '';
		$this->config->set('storage', 'objectstorage_endpoint', $endpoint);
		$this->endpoint = $endpoint;

		$bucket = $data['bucket'] ?? '';
		if ($bucket === '') {
			return [
				'bucket' => $this->l10n->t('Enter a bucket name')
			];
		};
		$this->config->set('storage', 'objectstorage_bucket', $bucket);
		$this->bucket = $bucket;

		$prefix = $data['prefix'] ?? '';
		$this->config->set('storage', 'objectstorage_prefix', $prefix);
		$this->prefix = $prefix;

		return [];
	}

	/**
	 * Get the AWS S3 region. Required for AWS S3.
	 */
	public function getRegion(): string
	{
		return $this->region;
	}

	/**
	 * Get the S3-endpoint. Required for non-AWS S3.
	 */
	public function getEndpoint(): string
	{
		return $this->endpoint;
	}

	/**
	 * Get the S3 bucket name. Required.
	 */
	public function getBucket(): string
	{
		return $this->bucket;
	}

	/**
	 * Get the bucket prefix. Optional, defaults to ''. When used, typically ends with '/' or '-'.
	 */
	public function getPrefix(): string
	{
		return $this->prefix;
	}
}
