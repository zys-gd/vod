<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.12.18
 * Time: 17:06
 */

namespace App\Domain\Service\VideoProcessing\Callback;


use Symfony\Component\HttpFoundation\Request;

interface CallbackHandlerInterface
{
    public function isSupports(Request $request): bool;

    public function handle(Request $request);
}