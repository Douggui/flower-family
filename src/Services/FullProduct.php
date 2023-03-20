<?php
namespace App\Services;

use App\Repository\CaracteristicDetailRepository;
use App\Repository\ImageRepository;
use App\Repository\OptionRepository;
use App\Repository\SpecificationRepository;


class FullProduct{
    
    private $optionRepo;
    private $caracDetailRepo;
    private $imageRepo;
    private $specRepo;
    
    
    public function __construct(SpecificationRepository $specRepo ,OptionRepository $optionRepo,CaracteristicDetailRepository $caracDetailRepo,ImageRepository $imageRepo)
    {
        $this->optionRepo=$optionRepo;
        $this->caracDetailRepo=$caracDetailRepo;
        $this->imageRepo=$imageRepo;
        $this->specRepo=$specRepo;
    }
    
    /* function return an array with all details of product($product) */
    public function getFullProductInformation($product){
        $id=$product->getId();
        $options=$this->optionRepo->findByProduct($id);
        $caracteristicDetail=$this->caracDetailRepo->findByProduct($id);
        $images=$this->imageRepo->findByProduct($id);
        //dd($this->specRepo->findOneBySubCategory($product->getSubCategory())->getName());
        $productInformation=[
            'product'=>$product,
            'options'=>$options,
            'caracteristicDetail'=>$caracteristicDetail,
            'images'=>$images,
            'subCategory'=>$product->getSubCategory()->getName(),
            'specification'=>$this->specRepo->findOneBySubCategory($product->getSubCategory())->getName()
            
            
        ];
        return $productInformation;
    }  
    
    /*function return an associative array with  all products of a subCategory */
    public function getProductsInformation($products){
        $productInformation=[];
        foreach ($products as $product) {
            $productInformation[]=[
                'product'=>$product,
                'images'=>$this->imageRepo->findByProduct($product),
                'options'=>$this->optionRepo->findByProduct($product),
                'caracteristicDetail'=>$this->caracDetailRepo->findByProduct($product)
            ];
            
        }
        return $productInformation;
    }
}

?>