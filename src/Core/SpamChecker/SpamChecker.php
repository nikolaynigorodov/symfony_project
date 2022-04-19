<?php

declare(strict_types=1);

namespace Future\Blog\Core\SpamChecker;

use Future\Blog\Core\Dto\ContactDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    public const SPAM = 1;
    public const BLATANT_SPAM = 2;

    private HttpClientInterface $client;

    private string $endpoint;

    public function __construct(HttpClientInterface $client, string $akismetKey)
    {
        $this->client = $client;
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    /**
     * @throws \RuntimeException if the call did not work
     * @return int Spam score: 0: not spam, 1: maybe spam, 2: blatant spam
     */
    public function getSpamScore(ContactDto $contact, array $context): int
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
                'comment_type' => 'contact',
                'comment_author' => $contact->getName(),
                'comment_author_email' => $contact->getEmail(),
                'comment_content' => $contact->getMessage(),
                'blog_lang' => 'en',
                'blog_charset' => 'UTF-8',
                'is_test' => true,
            ]),
        ]);

        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new \RuntimeException(sprintf('Unable to check for spam: %s (%s).', $content, $headers['x-akismet-debug-help'][0]));
        }

        return $content === 'true' ? 1 : 0;
    }
}
