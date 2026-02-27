<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Custom AddressBookHome that appends the GAL as a read-only
 * address book alongside the user's regular address books.
 */

namespace grommunio\DAV;

use Sabre\CardDAV\AddressBookHome;
use Sabre\CardDAV\Backend\BackendInterface;
use Sabre\DAV\INode;

class GalAddressBookHome extends AddressBookHome {
	private $galCache;
	private $gdavBackend;

	/**
	 * Constructor.
	 *
	 * @param string                   $principalUri
	 * @param null|GalCache            $galCache
	 * @param null|GrommunioDavBackend $gdavBackend
	 */
	public function __construct(BackendInterface $carddavBackend, $principalUri, $galCache = null, $gdavBackend = null) {
		parent::__construct($carddavBackend, $principalUri);
		$this->galCache = $galCache;
		$this->gdavBackend = $gdavBackend;
	}

	/**
	 * Returns all address books including the GAL.
	 *
	 * @return array
	 */
	public function getChildren() {
		$children = parent::getChildren();

		if ($this->galCache !== null && $this->gdavBackend !== null) {
			$children[] = new GalAddressBook(
				$this->galCache,
				$this->gdavBackend,
				$this->principalUri,
				new GLogger('gal')
			);
		}

		return $children;
	}

	/**
	 * Returns a specific child node by name.
	 *
	 * @param string $name
	 *
	 * @return INode
	 */
	public function getChild($name) {
		if ($name === GalAddressBook::URI && $this->galCache !== null && $this->gdavBackend !== null) {
			return new GalAddressBook(
				$this->galCache,
				$this->gdavBackend,
				$this->principalUri,
				new GLogger('gal')
			);
		}

		return parent::getChild($name);
	}
}
