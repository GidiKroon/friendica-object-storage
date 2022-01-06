<?php

namespace Friendica\Addon\objectstorage;

use Friendica\Core\Config\Capability\IManageConfigValues;
use Friendica\Core\L10n;
use Friendica\Core\Storage\Capability\ICanConfigureStorage;

class ObjectStorageBackendConfig implements ICanConfigureStorage
{
	/** @var IManageConfigValues */
	private $config;
	/** @var L10n */
	private $l10n;
	/** @var string */
	private $endpoint;

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
		$this->endpoint = $this->config->get('storage', 'objectstorage_endpoint', '');
	}

	public function getOptions(): array
	{
		return [
			'endpoint' => [
				'input',
				$this->l10n->t('The object storage endpoint'),
				$this->endpoint,
				$this->l10n->t('Enter the object storage endpoint, e.g. "eu-west-1.s3.amazonaws.com"'),
			],
		];
	}

	public function saveOptions(array $data): array
	{
		$endpoint = $data['endpoint'] ?? '';
		if ($endpoint === '') {
			return [
				'endpoint' => $this->l10n->t('Enter an endpoint')
			];
		};
		$this->config->set('storage', 'objectstorage_endpoint', $endpoint);
		$this->endpoint = $endpoint;
		return [];
	}

	public function getEndpoint(): string
	{
		return $this->endpoint;
	}
}
