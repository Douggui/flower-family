<?php

namespace App\Services;

use Knp\Component\Pager\PaginatorInterface;

class Paginator{
    
    private $paginator;
    
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator=$paginator;
    }
   
    public function pagination($data,$page,$nbreOfPage){
        $products=$this->paginator->paginate(
            $data,
            $page,
            $nbreOfPage
        );
        return $products;
    }
    
}
?>