<?php
/**
 * Created by: Derek Cameron <info@derekcameron.com>
 * Copyright: 2020 Derek Cameron
 * Date: 2020/12/27
 **/

namespace Concrete\Core\Http\Middleware;


use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class ContentSecurityMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * Apply a CSP of script-src to certain pages
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
            $response = $frame->next($request);
            if ($request->getPathInfo() !== '/') {
                $response->headers->set('Content-Security-Policy', "script-src 'self' 'unsafe-inline'");
            }


        return $response;
    }

}