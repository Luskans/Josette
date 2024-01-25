<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Story;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

final class ByThemeExtension implements QueryCollectionExtensionInterface
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