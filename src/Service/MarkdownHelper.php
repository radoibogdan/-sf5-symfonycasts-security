<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    private $markdownParser;
    private $cache;
    private $isDebug;
    private $logger;

    /**
     * Utile pour récupérer le user
     *
     * @var Security
     */
    private Security $security;
    private bool $isArgument;

    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache, bool $isDebug, LoggerInterface $mdLogger, Security $security, bool $isArgument)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
        $this->isDebug = $isDebug;
        $this->logger = $mdLogger;
        $this->security = $security;
        $this->isArgument = $isArgument;
    }

    public function parse(string $source): string
    {
        if ($this->isArgument) {
            dump('Je suis un argument dans services.yaml disponible que pour le service MarkdownHelper.');
        }

        # Check user connected v1
        if ($this->security->getUser()) {
            $this->logger->info('Markdown traité pour {user}', [
                'user' => $this->security->getUser()->getUserIdentifier()
            ]);
        }

        # Check user connected v2 + verify cookie remember me
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //
        }

        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Meow!');
        }

        if ($this->isDebug) {
            return $this->markdownParser->transformMarkdown($source);
        }

        return $this->cache->get('markdown_'.md5($source), function() use ($source) {
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}
