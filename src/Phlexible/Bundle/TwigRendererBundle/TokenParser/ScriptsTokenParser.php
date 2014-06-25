<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\TokenParser;

use Phlexible\Bundle\FrontendAssetBundle\Collector\Block;
use Phlexible\Bundle\FrontendAssetBundle\Collector\Collector;
use Phlexible\Bundle\TwigRendererBundle\Node\ScriptsNode;

/**
 * Scripts
 *
 * <pre>
 *  {% scripts head %}
 *    <script type="text/javascript" href="$url">
 *  {% endscripts %}
 * </pre>
 */
class ScriptsTokenParser extends \Twig_TokenParser
{
    /**
     * @var Collector
     */
    private $frontendAssetsCollector;

    /**
     * @param Collector $frontendAssetsCollector
     */
    public function __construct(Collector $frontendAssetsCollector)
    {
        $this->frontendAssetsCollector = $frontendAssetsCollector;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $collectionName = null;
        $output = null;
        $inputs = array();

        while (!$stream->isEOF()) {
            $current = $stream->getCurrent();
            if ($current->getType() === \Twig_Token::NAME_TYPE) {
                $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
                $target = $targets->getNode(0);
                $name = $target->getAttribute('name');
                $stream->expect(\Twig_Token::OPERATOR_TYPE);
                $seq = $this->parser->getExpressionParser()->parseExpression();
                $value = $seq->getAttribute('value');
                if ($name === 'name') {
                    $collectionName = $value;
                } elseif ($name === 'output') {
                    $output = $value;
                }
            } elseif ($current->getType() === \Twig_Token::STRING_TYPE) {
                $inputs[] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($current->getType() === \Twig_Token::BLOCK_END_TYPE) {
                $stream->next();
                break;
            } else {
                $stream->next();
            }
        }

        $body = $this->parser->subparse(array($this, 'decideScriptsEnd'), true);
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        if ($collectionName) {
            $blocks = $this->frontendAssetsCollector->collect();
            $block = $blocks->getBlock($collectionName);
        } else {
            $block = new Block(uniqid());
        }

        foreach ($inputs as $input) {
            $block->append($input);
        }

        if (!count($block->getFiles())) {
            return null;
        }

        $inputs = $block->getFiles();

        return new ScriptsNode($inputs, $output, $body, $lineno, $this->getTag());
    }

    public function decideScriptsEnd(\Twig_Token $token)
    {
        return $token->test('endscripts');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'scripts';
    }
}
