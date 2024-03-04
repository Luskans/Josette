<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Story;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Metadata\Operation;

// ...

final class ByLikesExtension implements QueryCollectionExtensionInterface
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
