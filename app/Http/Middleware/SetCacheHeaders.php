<?php
/**
 * Created by PhpStorm.
 * User: Louv
 * Date: 2019/9/27
 * Time: 14:26
 */

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Illuminate\Http\Request as IlluminateRequest;

use App;

use ES\Log\FluentLogger;

use Closure;
use DateTime;
use InvalidArgumentException;
use Exception;

/**
 *
 * 缓存时间头的设定，请参见：
 * @see https://docs.aws.amazon.com/zh_cn/AmazonCloudFront/latest/DeveloperGuide/Expiration.html
 *
 * 缓存 Vary 头目前使用 `Vary: Accept-Encoding,Appkey,Token` ，
 * 但 CloudFront 并不修改 Vary ，而是在管理端配置。请参见：
 * @see https://docs.aws.amazon.com/zh_cn/AmazonCloudFront/latest/DeveloperGuide/header-caching.html
 * */
class SetCacheHeaders
{
    /**
     * Header 强制修改缓存相关头
     *
     * @var string
     * @example DEV-CACHE-HEADER: \
     *          public;etag;max_age=121;s_maxage=120;expires=123456789;remove_cache_control;vary=Accept-Encoding,User-Agent
     * */
    public const DEV_CACHE_HEADER = 'DEV-CACHE-HEADER';

    /**
     * Add cache related HTTP headers.
     *
     * @param IlluminateRequest $request
     * @param Closure           $next
     * @param string|array      $options
     * @return SymfonyResponse
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function handle($request, Closure $next, $options = [])
    {
        /* @var SymfonyResponse $response */
        $response = $next($request);

        if (!$this->assertHostShouldUseCdnCache($request)) {
            return $response;
        }

        // DEV_CACHE_HEADER
        if (config('app.debug') && $devCacheHeader = $request->header(self::DEV_CACHE_HEADER)) {
            $options = $devCacheHeader;
        }

        if (!$request->isMethodCacheable() || !$response->getContent()) {
            return $response;
        }

        if (is_string($options)) {
            $options = $this->parseOptions($options);
        }

        if (isset($options['etag']) && $options['etag'] === true) {
            $options['etag'] = md5($response->getContent());
        }

        // Expires RFC7231
        // $options['expire'] 为 UTC 时间戳
        if (config('app.debug') && isset($options['expires']) && !empty($options['expires'])) {
            $response->setExpires(
                (new DateTime())->setTimestamp($options['expires'])
            );
            unset($options['expires']);
        }

        // Vary
        if (config('app.debug') && isset($options['vary']) && !empty($options['vary'])) {
            $response->setVary($options['vary']);
            unset($options['vary']);
        }
        $cache_control = $request->header('cache-control', '');
        // Remove cache-control
        if (
            config('app.debug') && isset($options['remove_cache_control']) && $options['remove_cache_control'] === true
        ) {
            $response->headers->remove('cache-control');
        } elseif ($cache_control != 'no-cache') {
            $response->setCache($options);
        }

        return $response;
    }

    /**
     * Parse the given header options.
     *
     * @param string $options
     * @return array
     */
    protected function parseOptions($options)
    {
        return collect(explode(';', $options))
            ->filter()
            ->mapWithKeys(function ($option) {
                $data = explode('=', $option, 2);
                return [$data[0] => $data[1] ?? true];
            })
            ->all();
    }

    /**
     * @param IlluminateRequest $request
     * @return bool
     * @see \Med\Http\Middleware\TrustProxies::$proxies Trust all chained proxies
     */
    protected function assertHostShouldUseCdnCache($request): bool
    {
        $host = $request->getHost();
        $enabledHosts = config('gen.cdn.cacheHeadersHosts.' . App::environment());

        FluentLogger::debug('middleware', [
            'logId' => 'bne2lo6bbh20m5ae3a5q40mv0k',
            'host' => $host,
            'enabledHosts' => $enabledHosts,
            'headers' => $request->headers->all(),
            'fullUrl' => $request->fullUrl(),
            'servers' => $request->server->all(),
        ]);

        return is_array($enabledHosts) && in_array($host, $enabledHosts);
    }
}
