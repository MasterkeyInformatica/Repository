<?php

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as BaseValidationException;

/**
 * ValidationException
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   06/03/2018
 */
class ValidationException extends BaseValidationException
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param   Request  $request
     * @return  $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return mixed
     */
    public function sendErrors()
    {
        if ( $this->request->ajax() ) {
            return response()->json([
                'errors'  => $this->errors()
            ], $this->getCode());
        }

        return redirect()->back()->withInput()->withErrors($this->errors());
    }
}