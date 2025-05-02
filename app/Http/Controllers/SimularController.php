<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Post(
 *     path="/api/simular",
 *     summary="Simula o empréstimo com base nos filtros",
 *     tags={"Simulação"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"valor_emprestimo"},
 *             @OA\Property(property="valor_emprestimo", type="number", format="float", example="1000", description="Valor do empréstimo a ser simulado"),
 *             @OA\Property(property="instituicoes", type="array", @OA\Items(type="string", example="Banco A"), description="Lista de instituições filtradas"),
 *             @OA\Property(property="convenios", type="array", @OA\Items(type="string", example="INSS"), description="Lista de convênios filtrados"),
 *             @OA\Property(property="parcela", type="number", format="int32", example="12", description="Número de parcelas do empréstimo")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Simulação realizada com sucesso",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="instituicao1", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="convenio", type="string", example="INSS"),
 *                     @OA\Property(property="parcelas", type="integer", example="12"),
 *                     @OA\Property(property="valor_parcela", type="number", format="float", example="150.25"),
 *                     @OA\Property(property="taxaJuros", type="number", format="float", example="1.25"),
 *                     @OA\Property(property="coeficiente", type="number", format="float", example="0.15")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro no processamento",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="erro", type="string", example="Erro ao processar os dados")
 *         )
 *     )
 * )
 */

class SimularController extends Controller
{
    public function simular(Request $request)
    {
        $request->validate([
            'valor_emprestimo' => 'required|numeric',
            'instituicoes' => 'array',
            'convenios' => 'array',
            'parcela' => 'numeric'
        ]);

        $valor = $request->valor_emprestimo;
        $instituicoesFiltro = $request->instituicoes;
        $conveniosFiltro = $request->convenios;
        $parcelaFiltro = $request->parcela;

        $raw = file_get_contents(base_path('storage/app/data/taxas_instituicoes.json'));

        if (!$raw) {
            return response()->json(['erro' => 'Arquivo nao encontrado'], 500);
        }

        $taxas = json_decode($raw, true);

        if (!$taxas) {
            return response()->json(['erro' => 'Erro ao decodificar JSON'], 500);
        }
        $resposta = [];

        foreach ($taxas as $taxa) {
            if (
                ($instituicoesFiltro && !in_array($taxa['instituicao'], $instituicoesFiltro)) ||
                ($conveniosFiltro && !in_array($taxa['convenio'], $conveniosFiltro)) ||
                ($parcelaFiltro && $taxa['parcelas'] != $parcelaFiltro)
            ) {
                continue;
            }

            $valorParcela = round($valor * $taxa['coeficiente'], 2);

            $resposta[$taxa['instituicao']][] = [
                'convenio' => $taxa['convenio'],
                'parcelas' => $taxa['parcelas'],
                'valor_parcela' => $valorParcela,
                'taxaJuros' => $taxa['taxaJuros'],
                'coeficiente' => $taxa['coeficiente']
            ];
        }

        return response()->json($resposta);
    }
}