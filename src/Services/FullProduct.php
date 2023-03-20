<?php
namespace App\Services;

use App\Repository\CaracteristicDetailRepository;
use App\Repository\ImageRepository;
use App\Repository\OptionRepository;
use App\Repository\SpecificationRepository;
use App\Repository\StockRepository;

class FullProduct{
    
    private $optionRepo;
    private $caracDetailRepo;
    private $imageRepo;
    private $specRepo;
    private $stockRepo;
    
    
    public function __construct(SpecificationRepository $specRepo ,OptionRepository $optionRepo,CaracteristicDetailRepository $caracDetailRepo,ImageRepository $imageRepo , StockRepository $stockRepo)
    {
        $this->optionRepo      = $optionRepo;
        $this->caracDetailRepo = $caracDetailRepo;
        $this->imageRepo       = $imageRepo;
        $this->specRepo        = $specRepo;
        $this->stockRepo       = $stockRepo;
    }
    
    /* function return an array with all details of product($product) */
    public function getFullProductInformation($product){
        $id=$product->getId();
        $options=$this->optionRepo->getProductOptions($product);

    //   dd($this->stockRepo->getStockOfProduct($product)); 
    //   dd($options); 
        // dd($this->stockRepo->getStockOfProduct($product));
        // foreach ($product->getStocks() as $option) {
        //     dd($option->getStock(),$product->getOptions());
        // }
       // dd($options);
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
                'caracteristicDetail'=>$this->caracDetailRepo->findByProduct($product)
            ];
        }
        return $productInformation;
    }
}

?>