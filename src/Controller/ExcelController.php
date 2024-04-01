<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/xls')]
class ExcelController extends AbstractController
{
    private $em;
    private $request;
    private $validator;

    function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        ValidatorInterface $validatorInterface
    ) {
        $this->em = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validatorInterface;
    }



    #[Route('/budgetXls', name: 'budgetXls')]
    public function budgetXls(ParameterBagInterface $params)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Arial'
            ]
        ]);

        $sheet = $spreadsheet->getActiveSheet()->setTitle('budget.title'); //////////////////////CODIGO PRESUPEUSTO MEJOR

        // Borders
        $topBorderThinStyle = ['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $rightBorderThinStyle = ['borders' => ['right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $bottomBorderThinStyle = ['borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $leftBorderThinStyle = ['borders' => ['left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $bordersThinStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];


        // Set height and width
        $sheet->getRowDimension(3)->setRowHeight(32);
        $sheet->getRowDimension(4)->setRowHeight(22);
        $sheet->getRowDimension(5)->setRowHeight(22);
        $sheet->getRowDimension(6)->setRowHeight(22);
        $sheet->getRowDimension(7)->setRowHeight(22);
        $sheet->getRowDimension(10)->setRowHeight(22);

        $sheet->getColumnDimensionByColumn(1)->setWidth(15);
        $sheet->getColumnDimensionByColumn(2)->setWidth(50);
        $sheet->getColumnDimensionByColumn(3)->setWidth(15);
        $sheet->getColumnDimensionByColumn(4)->setWidth(15);
        $sheet->getColumnDimensionByColumn(5)->setWidth(15);

        // Header
        // $sheet->getStyle([1, 4, 5, 4])->getAlignment()->setVertical('center');
        $sheet->getStyle([1, 3, 5, 7])->getAlignment()->setHorizontal('left');
        $sheet->getStyle([1, 3, 5, 7])->getAlignment()->setVertical('center');

        //HeaderCliente
        $sheet->mergeCells([1, 3, 2, 3]);
        $sheet->mergeCells([1, 4, 2, 4]);
        $sheet->mergeCells([1, 5, 2, 5]);
        $sheet->mergeCells([1, 5, 2, 5]);
        $sheet->mergeCells([1, 6, 2, 6]);
        $sheet->mergeCells([1, 7, 2, 7]);

        //Header Presupuesto
        $sheet->mergeCells([3, 3, 5, 3]);
        $sheet->mergeCells([3, 4, 5, 4]);
        $sheet->mergeCells([3, 5, 5, 5]);
        $sheet->mergeCells([3, 6, 5, 6]);
        $sheet->mergeCells([3, 7, 5, 7]);


        $sheet->getStyle([1, 3, 5, 3])->getFont()->setBold(true);
        $sheet->getStyle([1, 3, 5, 3])->getFont()->setSize(14);

        //Tabla
        $sheet->getStyle([1, 10, 5, 10])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([1, 10, 5, 10])->getFont()->getColor()->setRGB('ffffff');



        // DATOS ////

        $data = json_decode($this->request->getContent(), true);

        $budget = $this->em->getRepository('App\Entity\Budget')->findOneById($data['budgetID']);
        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);

        $sheet
            ->setCellValue([1, 3], 'Cliente')
            ->setCellValue([1, 4], $client->getName())
            ->setCellValue([1, 5], $client->getTaxIdentification())
            ->setCellValue([1, 6], "{$client->getAddress()} - {$client->getCity()}")
            ->setCellValue([1, 7], "{$client->getTlf()} - {$client->getContactEmail()}")

            ->setCellValue([3, 3], 'Presupuesto')
            // ->setCellValue([3, 4], $budget->getCode())   /////////////////////////////////////////////
            ->setCellValue([3, 4], 'CODIGO ????')
            ->setCellValue([3, 5], $budget->getDateTime())

            ->setCellValue([1, 10], 'Código')
            ->setCellValue([2, 10], 'Artículo')
            ->setCellValue([3, 10], 'Cantidad')
            ->setCellValue([4, 10], 'Precio')
            ->setCellValue([5, 10], 'Total');


        $total = 0;
        $currentRow = 11;

        foreach ($budget->getBudgetArticles() as $article) {   //////////////////////////////////////
            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $sheet
                ->setCellValue([1, $currentRow], $article->getArticleCode())
                ->setCellValue([2, $currentRow], $article->getNameArticle())
                ->setCellValue([3, $currentRow], $article->getQuantity())
                ->setCellValue([4, $currentRow], $article->getPrice())
                ->setCellValue([5, $currentRow], $article->getTotal());

            $total = $total + $article->getTotal();
            $currentRow++;
        }
        $currentRow--;

        // Table content
        $sheet->getStyle([5, 11, 5, $currentRow])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([1, $currentRow, 5, $currentRow])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([1, 10, 5, $currentRow])->getAlignment()->setVertical('center');
        $sheet->getStyle([1, 10, 5, $currentRow])->getAlignment()->setHorizontal('center');

        //Tabla IVA
        $sheet->getRowDimension($currentRow + 4)->setRowHeight(22);
        $sheet->getRowDimension($currentRow + 5)->setRowHeight(22);
        $sheet->getStyle([1, $currentRow + 4, 5, $currentRow + 4])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([1, $currentRow + 4, 5, $currentRow + 4])->getFont()->getColor()->setRGB('ffffff');

        $sheet
            ->setCellValue([2, $currentRow + 4], 'IVA %    ')
            ->setCellValue([2, $currentRow + 5], '21%')
            ->setCellValue([3, $currentRow + 4], 'IVA TOTAL')
            ->setCellValue([3, $currentRow + 5], '????')  /////////////////////////////////////////////////////////////
            ->setCellValue([4, $currentRow + 4], 'SUBTOTAL')
            ->setCellValue([4, $currentRow + 5], '????')  ///////////////////////////////////////////////////////////////
            ->setCellValue([5, $currentRow + 4], 'TOTAL')
            ->setCellValue([5, $currentRow + 5], $total);

        $sheet->getStyle([5, $currentRow + 4, 5, $currentRow + 5])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([1, $currentRow + 4, 5, $currentRow + 5])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 4, 5, $currentRow + 5])->getAlignment()->setVertical('center');
        $sheet->getStyle([2, $currentRow + 4, 2, $currentRow + 5])->getAlignment()->setHorizontal('right');
        $sheet->getStyle([3, $currentRow + 4, 5, $currentRow + 5])->getAlignment()->setHorizontal('center');


        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $path = "{$params->get('kernel.project_dir')}/var/xls/";
        $fileName = "presupuesto???.xlsx";

        $writer->save("{$path}{$fileName}");

        return $this->file("{$path}{$fileName}", $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT)->deleteFileAfterSend();
    }
}
