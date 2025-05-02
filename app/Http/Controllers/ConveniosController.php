<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 /**
 * @OA\Tag(
 *     name="Convênios",
 *     description="Operações relacionadas a convênios"
 * )
 */

 
/**
 * @OA\PathItem(
 *     path="/api/convenios"
 * )
 */

 /**
 * @OA\Get(
 *     path="/api/convenios",
 *     summary="Lista todos os convênios disponíveis",
 *     tags={"Convênios"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de convênios",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="chave", type="string", example="INSS"),
 *                 @OA\Property(property="valor", type="string", example="INSS")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao processar os dados",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="erro", type="string", example="Erro ao decodificar JSON")
 *         )
 *     )
 * )
 */
class ConveniosController extends Controller
{
    public function convenios()
    {
        $raw = file_get_contents(base_path('storage/app/data/convenios.json'));

        if (!$raw) {
            return response()->json(['erro' => 'Arquivo não encontrado'], 500);
        }

        $dados = json_decode($raw, true);

        if (!$dados) {
            return response()->json(['erro' => 'Erro ao decodificar JSON'], 500);
        }

        return response()->json($dados);
    }
}
