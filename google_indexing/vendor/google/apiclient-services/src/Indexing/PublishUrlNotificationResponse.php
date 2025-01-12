<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Indexing;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class PublishUrlNotificationResponse extends \Google\Model implements \Psr\Http\Message\RequestInterface
{
  protected $urlNotificationMetadataType = UrlNotificationMetadata::class;
  protected $urlNotificationMetadataDataType = '';

  /**
   * @param UrlNotificationMetadata
   */
  public function setUrlNotificationMetadata(UrlNotificationMetadata $urlNotificationMetadata)
  {
    $this->urlNotificationMetadata = $urlNotificationMetadata;
  }
  /**
   * @return UrlNotificationMetadata
   */
  public function getUrlNotificationMetadata()
  {
    return $this->urlNotificationMetadata;
  }

	public function getProtocolVersion()
	{
		// TODO: Implement getProtocolVersion() method.
	}

	public function withProtocolVersion($version)
	{
		// TODO: Implement withProtocolVersion() method.
	}

	public function getHeaders()
	{
		// TODO: Implement getHeaders() method.
	}

	public function hasHeader($name)
	{
		// TODO: Implement hasHeader() method.
	}

	public function getHeader($name)
	{
		// TODO: Implement getHeader() method.
	}

	public function getHeaderLine($name)
	{
		// TODO: Implement getHeaderLine() method.
	}

	public function withHeader($name, $value)
	{
		// TODO: Implement withHeader() method.
	}

	public function withAddedHeader($name, $value)
	{
		// TODO: Implement withAddedHeader() method.
	}

	public function withoutHeader($name)
	{
		// TODO: Implement withoutHeader() method.
	}

	public function getBody()
	{
		// TODO: Implement getBody() method.
	}

	public function withBody(StreamInterface $body)
	{
		// TODO: Implement withBody() method.
	}

	public function getRequestTarget()
	{
		// TODO: Implement getRequestTarget() method.
	}

	public function withRequestTarget($requestTarget)
	{
		// TODO: Implement withRequestTarget() method.
	}

	public function getMethod()
	{
		// TODO: Implement getMethod() method.
	}

	public function withMethod($method)
	{
		// TODO: Implement withMethod() method.
	}

	public function getUri()
	{
		// TODO: Implement getUri() method.
	}

	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		// TODO: Implement withUri() method.
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishUrlNotificationResponse::class, 'Google_Service_Indexing_PublishUrlNotificationResponse');
