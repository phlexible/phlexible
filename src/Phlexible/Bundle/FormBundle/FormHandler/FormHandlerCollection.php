<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\FormHandler;

/**
 * Form handler collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FormHandlerCollection
{
    /**
     * @var FormHandlerInterface[]
     */
    private $formHandlers = array();

    /**
     * @param FormHandlerInterface[] $formHandlers
     */
    public function __construct(array $formHandlers = array())
    {
        foreach ($formHandlers as $formHandler) {
            $this->addFormHandler($formHandler);
        }
    }

    /**
     * @param FormHandlerInterface $formHandler
     *
     * @return $this
     */
    public function addFormHandler(FormHandlerInterface $formHandler)
    {
        $this->formHandlers[$formHandler->getName()] = $formHandler;

        return $this;
    }

    /**
     * @param string $formName
     *
     * @return FormHandlerInterface
     */
    public function get($formName)
    {
        if (!$this->has($formName)) {
            return null;
        }

        return $this->formHandlers[$formName];
    }

    /**
     * @param string $formName
     *
     * @return bool
     */
    public function has($formName)
    {
        return isset($this->formHandlers[$formName]);
    }

    /**
     * @return FormHandlerInterface[]
     */
    public function all()
    {
        return $this->formHandlers;
    }
}
