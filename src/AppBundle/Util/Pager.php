<?php
namespace AppBundle\Util;

class Pager
{
    public static function create($total, $limit, $page)
    {
        $firstPage = 1;
        $lastPage = max(ceil($total / $limit), 1);
        $currentPage = max($firstPage, min($page, $lastPage));

        $sectionLength = 5;
        $sectionFirstPage = max($firstPage, min($lastPage - $sectionLength + 1, $currentPage - floor($sectionLength / 2)));
        $sectionLastPage = min(max($sectionLength, $currentPage + floor(($sectionLength - 1) / 2)), $lastPage);

        $pageList = array();

        if ($sectionFirstPage > $firstPage) {
            $pageList[] = $firstPage;
        }
        if ($sectionFirstPage > $firstPage + 1) {
            $pageList[] = '';
        }
        if ($sectionLastPage > $sectionFirstPage) {
            for ($p = $sectionFirstPage; $p <= $sectionLastPage; $p++) {
                $pageList[] = $p;
            }
        }
        if ($sectionLastPage < $lastPage - 1) {
            $pageList[] = '';
        }
        if ($sectionLastPage < $lastPage) {
            $pageList[] = $lastPage;
        }

        $result = array(
            'firstPage' => $firstPage,
            'lastPage' => $lastPage,
            'currentPage' => $currentPage,
            'pageList' => $pageList,
        );

        if ($currentPage > $firstPage) {
            $result['prevPage'] = $currentPage - 1;
        }
        if ($currentPage < $lastPage) {
            $result['nextPage'] = $currentPage + 1;
        }

        return $result;
    }
}