<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Story;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

final class ByReadingTimeExtension implements QueryCollectionExtensionInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass !== Story::class) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request->query->get('byReadingTime')) {
            $order = $request->query->get('order', 'DESC'); // Par défaut on trie en ordre croissant

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->addSelect(sprintf('LENGTH(%s.content) / 220 as HIDDEN readingTime', $rootAlias)) // 220 est une variable moyenne de mots lus par minute
                ->addOrderBy('readingTime', in_array($order, ['ASC', 'DESC']) ? $order : 'DESC');
        }
    }
}