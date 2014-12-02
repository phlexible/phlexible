<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Configurator;

use Symfony\Component\HttpFoundation\Response;

/**
 * Element renderer configuration
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenderConfiguration
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @var array
     */
    private $variables = array();

    /**
     * @var array
     */
    private $features = array();

    /**
     * @var Response
     */
    private $response;

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $feature
     *
     * @return $this
     */
    public function set($key, $value, $feature = null)
    {
        $this->values[$key] = $value;

        if ($feature) {
            $this->addFeature($feature);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $feature
     *
     * @return $this
     */
    public function setVariable($key, $value, $feature = null)
    {
        $this->variables[$key] = $value;

        $this->set($key, $value, $feature);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getVariable($key)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasVariable($key)
    {
        return in_array($key, $this->variables);
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function addFeature($name)
    {
        $this->features[] = $name;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFeature($name)
    {
        return in_array($name, $this->features);
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }
}
