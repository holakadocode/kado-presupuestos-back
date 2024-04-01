<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $data = json_decode($this->request->getContent(), true);

        $budget = $this->em->getRepository('App\Entity\Budget')->findOneById($data['budgetID']);
        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);
        $companies = $this->em->getRepository('App\Entity\Company')->findAll();

        if (!isset($companies[0])) {
            return new JsonResponse('No existe compañia', Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        $company = $companies[0];


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Arial'
            ]
        ]);

        $sheet = $spreadsheet->getActiveSheet()->setTitle('P-000' . $budget->getId());

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
        $sheet->getRowDimension(7)->setRowHeight(32);
        $sheet->getRowDimension(8)->setRowHeight(32);
        $sheet->getRowDimension(9)->setRowHeight(22);
        $sheet->getRowDimension(10)->setRowHeight(22);
        $sheet->getRowDimension(11)->setRowHeight(22);
        $sheet->getRowDimension(12)->setRowHeight(22);
        $sheet->getRowDimension(15)->setRowHeight(32);

        $sheet->getColumnDimensionByColumn(1)->setWidth(11);
        $sheet->getColumnDimensionByColumn(2)->setWidth(40);
        $sheet->getColumnDimensionByColumn(3)->setWidth(11);
        $sheet->getColumnDimensionByColumn(4)->setWidth(11);
        $sheet->getColumnDimensionByColumn(5)->setWidth(11);

        // Header
        $sheet->getStyle([1, 3, 5, 12])->getAlignment()->setHorizontal('left');
        $sheet->getStyle([1, 3, 5, 12])->getAlignment()->setVertical('center');

        //HeaderCliente
        $sheet->mergeCells([1, 3, 2, 3]);
        $sheet->mergeCells([1, 4, 2, 4]);
        $sheet->mergeCells([1, 5, 2, 5]);
        $sheet->mergeCells([1, 5, 2, 5]);
        $sheet->mergeCells([1, 6, 2, 6]);
        $sheet->mergeCells([1, 7, 2, 7]);
        $sheet->mergeCells([1, 8, 2, 8]);
        $sheet->mergeCells([1, 9, 2, 9]);
        $sheet->mergeCells([1, 10, 2, 10]);
        $sheet->mergeCells([1, 11, 2, 11]);
        $sheet->mergeCells([1, 12, 2, 12]);

        //Header Presupuesto
        $sheet->mergeCells([3, 8, 5, 8]);
        $sheet->mergeCells([3, 9, 5, 9]);
        $sheet->mergeCells([3, 10, 5, 10]);
        $sheet->mergeCells([3, 11, 5, 11]);
        $sheet->mergeCells([3, 12, 5, 12]);


        $sheet->getStyle([1, 3, 5, 3])->getFont()->setBold(true);
        $sheet->getStyle([1, 3, 5, 3])->getFont()->setSize(14);
        $sheet->getStyle([1, 8, 5, 8])->getFont()->setBold(true);
        $sheet->getStyle([1, 8, 5, 8])->getFont()->setSize(14);

        //Tabla
        $sheet->getStyle([1, 15, 5, 15])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([1, 15, 5, 15])->getFont()->getColor()->setRGB('ffffff');

        $sheet
            ->setCellValue([1, 3], $company->getName())
            ->setCellValue([1, 4], "NIF: {$company->getTaxIdentification()}")
            ->setCellValue([1, 5], "{$company->getAddress()} , CP: {$company->getCp()} , {$company->getCity()}")
            ->setCellValue([1, 6], "{$company->getPhone()} , {$company->getEmail()}")


            ->setCellValue([1, 8], 'Cliente')
            ->setCellValue([1, 9], $client->getName())
            ->setCellValue([1, 10], $client->getTaxIdentification())
            ->setCellValue([1, 11], "{$client->getAddress()} - {$client->getCity()}")
            ->setCellValue([1, 12], "{$client->getTlf()} - {$client->getContactEmail()}")

            ->setCellValue([3, 8], 'Presupuesto')
            ->setCellValue([3, 9], 'P-000' . $budget->getId())
            ->setCellValue([3, 10], $budget->getDateTime()->format('d/m/Y'))

            ->setCellValue([1, 15], 'Código')
            ->setCellValue([2, 15], 'Artículo')
            ->setCellValue([3, 15], 'Cantidad')
            ->setCellValue([4, 15], 'Precio')
            ->setCellValue([5, 15], 'Total');


        $subTotal = 0;
        $currentRow = 16;

        foreach ($budget->getBudgetArticles() as $article) {   
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
         
            $totalLinePrice = $article->getQuantity() * $article->getPrice();
            $sheet
            
                ->setCellValue([1, $currentRow], $article->getArticleCode())
                ->setCellValue([2, $currentRow], $article->getNameArticle())
                ->setCellValue([3, $currentRow], $article->getQuantity())
                ->setCellValue([4, $currentRow], $article->getPrice())
                ->setCellValue([5, $currentRow], $totalLinePrice);

            $subTotal = $subTotal + $totalLinePrice;
            $currentRow++;
        }
        $currentRow--;

        // Table content
        $sheet->getStyle([1, 15, 5, $currentRow])->applyFromArray($leftBorderThinStyle);
        $sheet->getStyle([5, 15, 5, $currentRow])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([1, $currentRow, 5, $currentRow])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([1, 15, 5, $currentRow])->getAlignment()->setVertical('center');
        $sheet->getStyle([1, 15, 5, $currentRow])->getAlignment()->setHorizontal('center');
        $sheet->getStyle([2, 16, 2, $currentRow])->getAlignment()->setHorizontal('left');

        //Tabla IVA
        $sheet->getRowDimension($currentRow + 9)->setRowHeight(22);
        $sheet->getRowDimension($currentRow + 10)->setRowHeight(22);
        $sheet->getStyle([1, $currentRow + 9, 5, $currentRow + 9])->getFill()->setFillType('solid')->getStartColor()->setRGB('373837');
        $sheet->getStyle([1, $currentRow + 9, 5, $currentRow + 9])->getFont()->getColor()->setRGB('ffffff');

        $sheet
            ->setCellValue([2, $currentRow + 9], 'IVA %    ')
            ->setCellValue([2, $currentRow + 10], '21%')
            ->setCellValue([3, $currentRow + 9], 'IVA TOTAL')
            ->setCellValue([3, $currentRow + 10], $subTotal * 0.21)  
            ->setCellValue([4, $currentRow + 9], 'SUBTOTAL')
            ->setCellValue([4, $currentRow + 10], $subTotal) 
            ->setCellValue([5, $currentRow + 9], 'TOTAL')
            ->setCellValue([5, $currentRow + 10], $subTotal * 1.21);

        $sheet->getStyle([1, $currentRow + 9, 1, $currentRow + 10])->applyFromArray($leftBorderThinStyle);
        $sheet->getStyle([5, $currentRow + 9, 5, $currentRow + 10])->applyFromArray($rightBorderThinStyle);
        $sheet->getStyle([1, $currentRow + 9, 5, $currentRow + 10])->applyFromArray($bottomBorderThinStyle);
        $sheet->getStyle([2, $currentRow + 9, 5, $currentRow + 10])->getAlignment()->setVertical('center');
        $sheet->getStyle([2, $currentRow + 9, 2, $currentRow + 10])->getAlignment()->setHorizontal('right');
        $sheet->getStyle([3, $currentRow + 9, 5, $currentRow + 10])->getAlignment()->setHorizontal('center');


        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $path = "{$params->get('kernel.project_dir')}/var/xls/";
        $fileName = "presupuesto???.xlsx";

        $writer->save("{$path}{$fileName}");

        return $this->file("{$path}{$fileName}", $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT)->deleteFileAfterSend();
    }
}
