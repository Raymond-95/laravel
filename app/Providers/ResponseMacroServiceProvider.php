<?php namespace App\Providers;

use Response;

use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('api', function($data = array(), $subject = '', $body = '', $status = 200, array $headers = array(), $options = 0)
        {
            $vars = get_class_vars('\Illuminate\Http\Response');
            $data = [
                'meta' => [
                    'status' => [
                        'code' => $status,
                        'desc' => $vars['statusTexts'][$status]
                    ],
                    'msg' => [
                        'subj' => $subject,
                        'body' => $body
                    ]
                ],
                'result' => $data
            ];          

            return Response::json($data, $status, $headers, $options);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
