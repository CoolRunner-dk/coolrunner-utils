<?php


namespace CoolRunner\Utils\Traits\Logging;


use App\Http\Middleware\InputLogger;
use App\Models\Company;

trait UseSlackHandler
{
    protected function context()
    {
        $context = [];

        return $this->addContext($context);
    }

    public function addContext(array $context) : array
    {
//        if ($company = Company::current()) {
//            $context += [
//                'company' => "#$company->id | $company->name",
//            ];
//        }
//
//        if ($log = InputLogger::getLog()) {
//            $context += [
//                'input log' => $log->uuid,
//            ];
//        }
//
        if ($user = \Auth::user()) {
                $identifier = $user->email ?: null;
                $context    += [
                    class_basename($user) => $identifier ? "#$user->id | $identifier" : "$user->id",
                ];

        }
        
        return $context;
    }
}
