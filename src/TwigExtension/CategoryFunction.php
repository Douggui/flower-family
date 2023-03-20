<?php
namespace App\TwigExtension;

use App\Repository\CategoryRepository;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class CategoryFunction extends AbstractExtension
{
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository=$categoryRepository;
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('categories', [$this, 'categories']),
        ];
    }

    public function categories()
    {
        return $this->categoryRepository->getCategoriesWithSubCategories();
    }
}