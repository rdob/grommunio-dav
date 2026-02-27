<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Custom AddressBookRoot that injects the GAL into each user's
 * address book home collection.
 */

namespace grommunio\DAV;

use Sabre\CardDAV\AddressBookRoot;
use Sabre\CardDAV\Backend\BackendInterface;
use Sabre\DAV\INode;
use Sabre\DAVACL\PrincipalBackend\BackendInterface as PrincipalBackendInterface;

class GalAddressBookRoot extends AddressBookRoot {
	private $gdavBackend;
	private $galCache;

	/**
	 * Constructor.
	 *
	 * @param string $principalPrefix
	 */
	public function __construct(PrincipalBackendInterface $principalBackend, BackendInterface $carddavBackend, GrommunioDavBackend $gdavBackend, $principalPrefix = 'principals') {
		parent::__construct($principalBackend, $carddavBackend, $principalPrefix);
		$this->gdavBackend = $gdavBackend;

		if (defined('GAL_ENABLED') && GAL_ENABLED && strlen(SYNC_DB) > 0) {
			$logger = new GLogger('gal');
			$ttl = defined('GAL_CACHE_TTL') ? GAL_CACHE_TTL : 3600;
			$this->galCache = new GalCache(SYNC_DB, $ttl, $logger);
		}
	}

	/**
	 * Returns a GalAddressBookHome for the given principal, which
	 * includes the GAL alongside regular address books.
	 *
	 * @return INode
	 */
	public function getChildForPrincipal(array $principal) {
		return new GalAddressBookHome(
			$this->carddavBackend,
			$principal['uri'],
			$this->galCache,
			$this->gdavBackend
		);
	}
}
