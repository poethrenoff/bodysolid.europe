<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;

use AdminUploadBundle\Util\Util;

use AppBundle\Entity\Brand;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPicture;
use AppBundle\Entity\ProductFile;
use AppBundle\Entity\ProductProperty;

/**
 * Class CatalogImportCommand
 */
class CatalogImportCommand extends DoctrineCommand
{
    /**
     * @var string
     */
    const IMPORT_URL = 'https://neotren.ru/xml.php';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:catalog:import');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $this->getContainer()->get('kernel')->getCacheDir() . '/neotren.xml';
        if (!file_exists($filename)) {
            file_put_contents($filename, file_get_contents(self::IMPORT_URL));
        }

        $import = @simplexml_load_file($filename);
        if (!$import) {
            return;
        }

        //$this->clearDb();

        $entityManager = $this->getEntityManager('default');

        $brandMap = [];
        $brandList = $entityManager->getRepository(Brand::class)->findAll();
        foreach ($brandList as $brand) {
            $brandMap[$brand->getTitle()] = $brand;
        }

        $categoryExternal = [];
        foreach ($import->shop->categories->category as $categoryXml) {
            $categoryExternal[] = (string)$categoryXml->attributes()->id;
        }
        $categoryMap = [];
        $categoryList = $entityManager->getRepository(Category::class)->findAll();
        foreach ($categoryList as $category) {
            if ($category->getExternalId()) {
                $categoryMap[$category->getExternalId()] = $category
                    ->setActive(in_array($category->getExternalId(), $categoryExternal));
            }
        }

        $productExternal = [];
        foreach ($import->shop->offers->offer as $offerXml) {
            $productExternal[] = (string)$offerXml->attributes()->id;
        }
        $productMap = [];
        $productList = $entityManager->getRepository(Product::class)->findAll();
        foreach ($productList as $product) {
            if ($product->getExternalId()) {
                $productMap[$product->getExternalId()] = $product
                    ->setActive(in_array($product->getExternalId(), $productExternal));
            }
        }

        $pictureSort = [];
        $fileSort = [];
        $propertySort = [];
        foreach ($import->shop->offers->offer as $offerXml) {

            if (!isset($categoryMap[(string)$offerXml->categoryId])) {
                continue;
            }

            if (!isset($brandMap[(string)$offerXml->vendor])) {
                $brand = (new Brand())
                    ->setTitle((string)$offerXml->vendor);
                $entityManager->persist($brand);

                $brandMap[(string)$offerXml->vendor] = $brand;
            }

            if (!isset($productMap[(string)$offerXml->attributes()->id])) {
                $product = (new Product())
                    ->setExternalId((string)$offerXml->attributes()->id)
                    ->setCategory(
                        $categoryMap[(string)$offerXml->categoryId]
                    )
                    ->setBrand(
                        $brandMap[(string)$offerXml->vendor]
                    )
                    ->setTitle((string)$offerXml->name)
                    ->setPrice((string)$offerXml->price)
                    ->setDescription((string)$offerXml->description)
                    ->setStatus($this->resolveStatus((string)$offerXml->status));
                $entityManager->persist($product);

                foreach ($offerXml->picture as $imageXml) {
                    $picturePath = $this->upload((string)$imageXml, ProductPicture::class, 'image');
                    $productPicture = (new ProductPicture())
                        ->setImage($picturePath)
                        ->setProduct($product)
                        ->setSort(
                            @ ++$pictureSort[(string)$offerXml->attributes()->id] * 10
                        );
                    $entityManager->persist($productPicture);
                }
                foreach ($offerXml->extImages->image as $imageXml) {
                    $picturePath = $this->upload((string)$imageXml, ProductPicture::class, 'image');
                    $productPicture = (new ProductPicture())
                        ->setImage($picturePath)
                        ->setProduct($product)
                        ->setSort(
                            @ ++$pictureSort[(string)$offerXml->attributes()->id] * 10
                        );
                    $entityManager->persist($productPicture);
                }
                foreach ($offerXml->salesArguments->argument as $imageXml) {
                    $picturePath = $this->upload((string)$imageXml->image, ProductPicture::class, 'image');
                    $productPicture = (new ProductPicture())
                        ->setImage($picturePath)
                        ->setProduct($product)
                        ->setSort(
                            @ ++$pictureSort[(string)$offerXml->attributes()->id] * 10
                        );
                    $entityManager->persist($productPicture);
                }
                foreach ($offerXml->urlManual as $fileXml) {
                    $filePath = $this->upload((string)$fileXml, ProductFile::class, 'file');
                    $productFile = (new ProductFile())
                        ->setFile($filePath)
                        ->setProduct($product)
                        ->setSort(
                            @ ++$fileSort[(string)$offerXml->attributes()->id] * 10
                        );
                    $entityManager->persist($productFile);
                }
                foreach ($offerXml->attrs->attr as $attrXml) {
                    $productProperty = (new ProductProperty())
                        ->setProduct($product)
                        ->setName((string)$attrXml->attributes()->key)
                        ->setTitle((string)$attrXml->attributes()->name)
                        ->setValue((string)$attrXml)
                        ->setSort(
                            @ ++$propertySort[(string)$offerXml->attributes()->id] * 10
                        );
                    $entityManager->persist($productProperty);
                }

                $productMap[(string)$offerXml->attributes()->id] = $product;
            } else {
                $productMap[(string)$offerXml->attributes()->id]
                    ->setTitle((string)$offerXml->name)
                    ->setPrice((string)$offerXml->price)
                    ->setDescription((string)$offerXml->description)
                    ->setStatus($this->resolveStatus((string)$offerXml->status));
            }
        }

        $entityManager->flush();
    }

    /**
     * @param string $status
     * @return string
     */
    protected function resolveStatus(string $status)
    {
        if (stripos($status, 'В наличии') !== false) {
            return 'available';
        }
        if (stripos($status, 'Ожидается') !== false) {
            return 'delivery';
        }
        return 'order';
    }

    /**
     * @param string $url
     * @param string $class
     * @param string $field
     * @return string
     */
    protected function upload(string $url, string $class, string $field)
    {
        $directory = $this->getContainer()->getParameter('admin_upload')['entities'][$class][$field]['directory'];
        $alias = $this->getContainer()->getParameter('admin_upload')['entities'][$class][$field]['alias'];

        $name = pathinfo($url, PATHINFO_BASENAME);
        $path = $directory . DIRECTORY_SEPARATOR . $name;
        if (!file_exists($path)) {
            $data = @file_get_contents($url);
            if ($data) {
                @file_put_contents($path, $data);
            }
        }

        return Util::getFilePath($name, $alias);
    }

    /**
     * Clear database
     */
    protected function clearDb()
    {
        $connection = $this->getDoctrineConnection('default');

        $connection->executeUpdate('SET foreign_key_checks = 0');
        $connection->executeUpdate('truncate brand');
        $connection->executeUpdate('truncate product');
        $connection->executeUpdate('truncate product_picture');
        $connection->executeUpdate('truncate product_file');
        $connection->executeUpdate('truncate product_video');
        $connection->executeUpdate('truncate product_property');
        $connection->executeUpdate('SET foreign_key_checks = 1');
    }
}