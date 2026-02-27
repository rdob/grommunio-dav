<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Read-only vCard node for a Global Address List entry.
 */

namespace grommunio\DAV;

use Sabre\CardDAV\ICard;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAVACL\ACLTrait;
use Sabre\DAVACL\IACL;

class GalCard implements ICard, IACL {
	use ACLTrait;

	private $uri;
	private $vcardData;
	private $etag;
	private $size;
	private $principalUri;

	/**
	 * Constructor.
	 *
	 * @param string $uri
	 * @param string $vcardData
	 * @param string $etag
	 * @param int    $size
	 * @param string $principalUri
	 */
	public function __construct($uri, $vcardData, $etag, $size, $principalUri) {
		$this->uri = $uri;
		$this->vcardData = $vcardData;
		$this->etag = $etag;
		$this->size = $size;
		$this->principalUri = $principalUri;
	}

	/**
	 * Returns the name of the node (the URI).
	 *
	 * @return string
	 */
	public function getName() {
		return $this->uri;
	}

	/**
	 * Renames the node -- not allowed.
	 *
	 * @param string $name
	 */
	public function setName($name) {
		throw new Forbidden('The Global Address List is read-only');
	}

	/**
	 * Returns the last modification time as a unix timestamp.
	 *
	 * @return null|int
	 */
	public function getLastModified() {
		return null;
	}

	/**
	 * Deletes the node -- not allowed.
	 */
	public function delete() {
		throw new Forbidden('The Global Address List is read-only');
	}

	/**
	 * Updates the vCard data -- not allowed.
	 *
	 * @param resource|string $data
	 *
	 * @return null|string
	 */
	public function put($data) {
		throw new Forbidden('The Global Address List is read-only');
	}

	/**
	 * Returns the vCard data.
	 *
	 * @return string
	 */
	public function get() {
		return $this->vcardData;
	}

	/**
	 * Returns the MIME type.
	 *
	 * @return string
	 */
	public function getContentType() {
		return 'text/vcard; charset=utf-8';
	}

	/**
	 * Returns the ETag.
	 *
	 * @return string
	 */
	public function getETag() {
		return $this->etag;
	}

	/**
	 * Returns the size in bytes.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Returns the owner principal.
	 *
	 * @return null|string
	 */
	public function getOwner() {
		return $this->principalUri;
	}

	/**
	 * Returns the read-only ACL.
	 *
	 * @return array
	 */
	public function getACL() {
		return [
			[
				'privilege' => '{DAV:}read',
				'principal' => '{DAV:}authenticated',
				'protected' => true,
			],
		];
	}
}
