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



    #[Route('/test', name: 'testxls')]
    public function test(ParameterBagInterface $params)
    {
        // Create the spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'size' => 12,
                'name' => 'Arial'
            ]
        ]);

        // Sheet title
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Portada');

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
        $bordersThickStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        // Merge
        $sheet->mergeCells([4, 15, 8, 18]);

        // Borders
        $sheet->getStyle([2, 2, 10, 2])->applyFromArray($topBorderThinStyle);
        $sheet->getStyle([10, 2, 10, 37])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([2, 37, 10, 37])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([2, 2, 2, 37])->applyFromArray($leftBorderThinStyle);
        $sheet->getStyle([2, 2, 10, 37])->getFill()->setFillType('solid')->getStartColor()->setRGB('ffffff');
        $sheet->getStyle([4, 15, 8, 18])->applyFromArray($bordersThickStyle);

        // Width
        $sheet->getColumnDimensionByColumn(2)->setWidth(12);
        $sheet->getColumnDimensionByColumn(3)->setWidth(12);
        $sheet->getColumnDimensionByColumn(4)->setWidth(12);
        $sheet->getColumnDimensionByColumn(5)->setWidth(12);
        $sheet->getColumnDimensionByColumn(6)->setWidth(12);
        $sheet->getColumnDimensionByColumn(7)->setWidth(12);
        $sheet->getColumnDimensionByColumn(8)->setWidth(12);
        $sheet->getColumnDimensionByColumn(9)->setWidth(12);
        $sheet->getColumnDimensionByColumn(10)->setWidth(12);

        // General style
        $sheet->getStyle([4, 15])->getAlignment()->setHorizontal('center');
        $sheet->getStyle([4, 15])->getAlignment()->setVertical('center');
        $sheet->getStyle([4, 15])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([4, 15])->getFont()->setSize(24);
        $sheet->getStyle([4, 15])->getFont()->setBold(true);
        $sheet->getStyle([4, 15])->getFont()->getColor()->setRGB('ffffff');

        $sheet->getStyle([5, 20])->getFont()->setSize(9);
        $sheet->getStyle([5, 22])->getFont()->setSize(9);

        // DATOS
        $client = $this->em->getRepository('App\Entity\Client')->findOneById(1);
        
        // Values
        $sheet
            ->setCellValue([4, 15], 'Presupuesto')
            ->setCellValue([4, 20], 'Cliente: ')
            ->setCellValue([5, 20], $client->getName().' '.$client->getSurname())
            ->setCellValue([4, 22], 'Fecha: ')
            ->setCellValue([5, 22], 'fecha ??');

        // Create another sheet
        $spreadsheet->createSheet(1);
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet()->setTitle('budget.title');

        $spreadsheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Arial'
            ]
        ]);

        // Set height and width
        $sheet->getRowDimension(6)->setRowHeight(22);
        $sheet->getColumnDimensionByColumn(1)->setWidth(15);
        $sheet->getColumnDimensionByColumn(2)->setWidth(50);
        $sheet->getColumnDimensionByColumn(3)->setWidth(12);
        $sheet->getColumnDimensionByColumn(4)->setWidth(15);
        $sheet->getColumnDimensionByColumn(5)->setWidth(15);

        // Table header
        $sheet->getStyle([1, 6, 5, 6])->applyFromArray($bordersThinStyle);
        $sheet->getStyle([1, 6, 5, 6])->getAlignment()->setVertical('center');
        $sheet->getStyle([1, 6, 5, 6])->getAlignment()->setHorizontal('center');
        $sheet->getStyle([1, 6, 5, 6])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([1, 6, 5, 6])->getFont()->setBold(true);
        $sheet->getStyle([1, 6, 5, 6])->getFont()->getColor()->setRGB('ffffff');

        $sheet
            ->setCellValue([1, 6], 'Código')
            ->setCellValue([2, 6], 'Artículo')
            ->setCellValue([3, 6], 'Cantidad')
            ->setCellValue([4, 6], 'Precio unidad')
            ->setCellValue([5, 6], 'Total');

    

        $total = 0;
        $currentRow = 7;

        foreach ($client->getBudgets() as $budget) {
            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $sheet
                // ->setCellValue([1, $currentRow], $budget['id']) método JAIME
                ->setCellValue([1, $currentRow], $budget->getId())
                ->setCellValue([2, $currentRow], $budget->getTitle())
                ->setCellValue([3, $currentRow], '2')
                ->setCellValue([4, $currentRow], '600 €')
                ->setCellValue([5, $currentRow], $budget->getTotal());

            $total=$total + $budget->getTotal();
            $currentRow++;
        }
        $currentRow--;


        // Table content
        $sheet->getStyle([5, 7, 5, $currentRow])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([1, $currentRow, 5, $currentRow])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([1, 7, 5, $currentRow])->getAlignment()->setVertical('center');
        $sheet->getStyle([1, 7, 1, $currentRow])->getAlignment()->setHorizontal('center');
        $sheet->getStyle([3, 7, 5, $currentRow])->getAlignment()->setHorizontal('center');
        // $sheet->getStyle([3, 7, 5, 9])->getAlignment()->setWrapText(true);   -----------------



        // Observations
        $sheet->getStyle([2, $currentRow + 4])->applyFromArray($topBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 4, 2, $currentRow + 8])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 8])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 4, 2, $currentRow + 8])->applyFromArray($leftBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 4, 2, $currentRow + 8])->getFill()->setFillType('solid')->getStartColor()->setRGB('ffffff');
        $sheet->getStyle([2, $currentRow + 4])->getFont()->setBold(true);

        $sheet->setCellValue([2, $currentRow + 4], 'Observaciones:');
        $sheet->mergeCells([2, $currentRow + 5, 2, $currentRow + 8]);


        // Observations
        $sheet->getStyle([4, $currentRow + 4, 5, $currentRow + 4])->applyFromArray($topBorderThinStyle);
        $sheet->getStyle([5, $currentRow + 4, 5, $currentRow + 8])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([4, $currentRow + 8, 5, $currentRow + 8])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([4, $currentRow + 4, 4, $currentRow + 8])->applyFromArray($leftBorderThinStyle);
        $sheet->getStyle([4, $currentRow + 4, 5, $currentRow + 8])->getFill()->setFillType('solid')->getStartColor()->setRGB('ffffff');
        $sheet->getStyle([4, $currentRow + 8, 5, $currentRow + 8])->getFont()->setBold(true);
        $sheet->getStyle([4, $currentRow + 4, 5, $currentRow + 8])->getAlignment()->setHorizontal('center');

        $sheet->setCellValue([4, $currentRow + 4], 'IVA');
        $sheet->setCellValue([5, $currentRow + 4], '??? %');
        $sheet->setCellValue([4, $currentRow + 5], 'IVA TOTAL');
        $sheet->setCellValue([5, $currentRow + 5], '??? €');
        $sheet->setCellValue([4, $currentRow + 6], 'Subtotal');
        $sheet->setCellValue([5, $currentRow + 6], '??? €');
        $sheet->setCellValue([4, $currentRow + 8], 'TOTAL');
        $sheet->setCellValue([5, $currentRow + 8], $total. ' €');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $path = "{$params->get('kernel.project_dir')}/var/xls/";
        $fileName = "presupuesto???.xlsx";

        $writer->save("{$path}{$fileName}");

        return $this->file("{$path}{$fileName}", $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT)->deleteFileAfterSend();
    }
}
