<?php


namespace App\Service\Helpers;


use App\Entity\Product;

class GeneratePaginatedEntityList
{
    /**
     * Separamos este servicio para su posible reutilización en caso de implementar un listado en la web, por ejemplo
     *
     * @param $paginatedEntityList
     * @param $page
     * @param $limit
     * @return array
     */

    public function __invoke($paginatedEntityList, $page, $limit): array
    {
        $maxPages = ceil(count($paginatedEntityList) / $limit);

        $records = [];
        $i = 0;

        //var_dump($paginatedEntityList);die();

        foreach ($paginatedEntityList as $entity) {
            if (method_exists ($entity, 'toArray') ) {
                $records[$i] = $entity->toArray();
            } else {
                $class = get_class($entity);
                $records['error'] = 'Falta implementar en la entidad ' . $class . ' el metodo toArray!';
                break;
            }
            $i++;
        }

        return $listing =  array(
            'records' => $records,
            'maxPages' => $maxPages,
            'thisPage' => $page,
        );
    }
}