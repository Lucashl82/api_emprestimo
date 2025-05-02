<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
     * @OA\Get(
     *     path="/api/instituicoes",
     *     summary="Lista todas as instituições",
     *     tags={"Instituições"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de instituições",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="chave", type="string", example="PAN"),
     *                 @OA\Property(property="valor", type="string", example="Pan")
     *             )
     *         )
     *     )
     * )
     */
class InstituicoesController extends Controller
{
    public function instituicoes()
    {
        $raw = file_get_contents(base_path('storage/app/data/instituicoes.json'));

        if (!$raw) {
            return response()->json(['erro' => 'Arquivo nao encontrado'], 500);
        }

        $dados = json_decode($raw, true);

        if (!$dados) {
            return response()->json(['erro' => 'Erro ao decodificar JSON'], 500);
        }

        return response()->json($dados);
    }
}