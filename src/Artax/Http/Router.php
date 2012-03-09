<?php

/**
 * Artax Route Router Class File
 * 
 * PHP version 5.4
 * 
 * @category   Artax
 * @package    blocks
 * @subpackage http
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */

namespace Artax\Http {
    
    use Artax\Routing\Matcher;
    use Artax\Routing\RequestInterface;
    use Artax\Routing\RouteInterface;
    use Artax\Routing\RouteList;

    /**
     * Route Router Class
     * 
     * @category   Artax
     * @package    blocks
     * @subpackage http
     * @author     Daniel Lowrey <rdlowrey@gmail.com>
     */
    class Router extends Matcher {

        /**
         * Injects request and route list dependencies
         * 
         * @param HttpRequest      $request   The Request object to match
         * @param RouteList        $routeList The list of routes to match against
         * 
         * @return void
         */
        public function __construct(
            HttpRequest $request,
            RouteList $routeList)
        {
            parent::__construct($request, $routeList);
        }

        /**
         * Extends parent to allow HTTP method route constraint
         * 
         * @param RequestInterface $request The request to match
         * @param RouteInterface   $route   The route to match against
         * 
         * @return bool Returns `TRUE` on match or `FALSE` if no match
         */
        protected function matchRoute(
            RequestInterface $request,
            RouteInterface $route)
        {
            $constraints = $route->getConstraints();
            $httpMethod = $request->getMethod();

            if (!empty($constraints['_method'])
                    && $constraints['_method'] !== $httpMethod) {
                return FALSE;
            } else {
                return parent::matchRoute($request, $route);
            }
        }

    }

}