<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Story;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\RequestStack;

final class ThemeFilterExtension implements QueryCollectionExtensionInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        if ($resourceClass !== Story::class) {
            return;
        }
        
        $request = $this->requestStack->getCurrentRequest();
        
        $themeName = $request->query->get('themeName');
        if ($themeName) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $themeAlias = $queryNameGenerator->generateJoinAlias('theme');
            
            $queryBuilder
                ->leftJoin(sprintf('%s.themes', $rootAlias), $themeAlias)
                ->andWhere(sprintf('%s.name = :themeName', $themeAlias))
                ->setParameter('themeName', $themeName);
        }
    }
}