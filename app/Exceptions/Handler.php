<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle custom SPK exceptions
        if ($exception instanceof \App\Exceptions\SpkNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $exception->getMessage()], 404);
            }
            return redirect()->route('spk.index')->with('error', $exception->getMessage());
        }

        if ($exception instanceof \App\Exceptions\SpkCreationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $exception->getMessage(),
                    'errors' => $exception->getErrors()
                ], 422);
            }
        }

        if ($exception instanceof \App\Exceptions\InvalidSpkDataException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $exception->getMessage()], 422);
            }
        }

        // Handle Pelanggan exceptions
        if ($exception instanceof \App\Exceptions\PelangganNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $exception->getMessage()], 404);
            }
        }

        if ($exception instanceof \App\Exceptions\InvalidPelangganDataException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $exception->getMessage()], 422);
            }
        }

        // Handle other module exceptions
        $exceptionClasses = [
            \App\Exceptions\PemasokNotFoundException::class,
            \App\Exceptions\InvalidPemasokDataException::class,
            \App\Exceptions\PembelianNotFoundException::class,
            \App\Exceptions\InvalidPembelianDataException::class,
            \App\Exceptions\KaryawanNotFoundException::class,
            \App\Exceptions\InvalidKaryawanDataException::class,
            \App\Exceptions\BahanBakuNotFoundException::class,
            \App\Exceptions\InvalidBahanBakuDataException::class,
            \App\Exceptions\MesinNotFoundException::class,
            \App\Exceptions\InvalidMesinDataException::class,
            \App\Exceptions\ProdukNotFoundException::class,
            \App\Exceptions\InvalidProdukDataException::class,
            \App\Exceptions\GudangNotFoundException::class,
            \App\Exceptions\InvalidGudangDataException::class,
            \App\Exceptions\RakNotFoundException::class,
            \App\Exceptions\InvalidRakDataException::class,
        ];

        foreach ($exceptionClasses as $exceptionClass) {
            if ($exception instanceof $exceptionClass) {
                if ($request->expectsJson()) {
                    $statusCode = strpos($exceptionClass, 'NotFoundException') !== false ? 404 : 422;
                    return response()->json(['error' => $exception->getMessage()], $statusCode);
                }
            }
        }

        return parent::render($request, $exception);
    }
}