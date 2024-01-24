<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Story;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\RequestStack;

// ...

final class ByLikesExtension implements QueryCollectionExtensionInterface, QueryResultCollectionExtensionInterface
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
        if ($request->query->get('byLikes')) {
            $order = $request->query->get('order', 'DESC'); // Valeur par défaut si le paramètre 'order' n'est pas présent

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $likeAlias = $queryNameGenerator->generateJoinAlias('like');
            $queryBuilder
                ->leftJoin(sprintf('%s.likes', $rootAlias), $likeAlias)
                ->addSelect(sprintf('COUNT(%s.id) as HIDDEN likeCount', $likeAlias))
                ->groupBy(sprintf('%s.id', $rootAlias))
                ->addOrderBy('likeCount', in_array($order, ['ASC', 'DESC']) ? $order : 'DESC');
        }
    }

    // La même logique peut être appliquée à applyToItem si nécessaire
}
