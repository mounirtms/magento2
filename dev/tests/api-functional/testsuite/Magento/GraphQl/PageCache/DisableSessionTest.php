<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
declare(strict_types=1);

namespace Magento\GraphQl\PageCache;

use Magento\Framework\App\PageCache\Version;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test absence/presence of private_content_version cookie in GraphQl POST HTTP responses
 */
class DisableSessionTest extends GraphQlAbstract
{
    private const PHPSESSID_COOKIE_NAME = 'PHPSESSID';

    #[
        Config('graphql/session/disable', '1')
    ]
    public function testPrivateSessionContentCookieNotPresentWhenSessionDisabled()
    {
        $result = $this->graphQlMutationWithResponseHeaders($this->getMutation());
        $this->assertArrayHasKey('headers', $result);
        if (!empty($result['headers']['Set-Cookie'])) {
            $this->assertFalse(
                $this->isCookieSet($result['headers']['Set-Cookie'], self::PHPSESSID_COOKIE_NAME),
                self::PHPSESSID_COOKIE_NAME . ' should not be present in HTTP response'
            );

            $this->assertFalse(
                $this->isCookieSet($result['headers']['Set-Cookie'], Version::COOKIE_NAME),
                Version::COOKIE_NAME . ' should not be present in HTTP response'
            );
        }
    }

    #[
        Config('graphql/session/disable', '0')
    ]
    public function testPrivateSessionContentCookiePresentWhenSessionEnabled()
    {
        $result = $this->graphQlMutationWithResponseHeaders($this->getMutation());
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('Set-Cookie', $result['headers'], 'Set-Cookie HTTP response header should be present');

        $this->assertTrue(
            $this->isCookieSet($result['headers']['Set-Cookie'], self::PHPSESSID_COOKIE_NAME),
            self::PHPSESSID_COOKIE_NAME . ' should be present in HTTP response'
        );

        $this->assertTrue(
            $this->isCookieSet($result['headers']['Set-Cookie'], Version::COOKIE_NAME),
            Version::COOKIE_NAME . ' should be present in HTTP response'
        );
    }

    /**
     * Checks if $cookieName was set by server in any of Set-Cookie header(s)
     *
     * @param array $setCookieHeader
     * @param string $cookieName
     * @return bool
     */
    private function isCookieSet(array $setCookieHeader, string $cookieName): bool
    {
        return count(array_filter($setCookieHeader, function ($cookie) use ($cookieName) {
            return str_starts_with($cookie, $cookieName);
        })) > 0;
    }

    /**
     * Provides dummy mutation to test GraphQl HTTP POST response
     *
     * @return string
     */
    private function getMutation(): string
    {
        return <<<GRAPHQL
mutation {
  createEmptyCart
}
GRAPHQL;
    }
}
