<?php


namespace app\Command;


use app\Core\ConsoleCommandInterface;
use app\Core\Container;
use app\Core\Rout;

class DebugRoutesCommand implements ConsoleCommandInterface
{
    private $name = 'mvc:router';
    private $args = [];
    private $output = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    public function run(Container $container = null): array
    {
        try{
            $routes = $container->getRoutes();
            $tableArr = [];
            $tableArr['headers'] = 'name,method,route,controller,action';
            $headers = explode(',', $tableArr['headers']);

            foreach ($headers as $header){
                $tableArr[$header] = [];
                $tableArr['chars'][$header] = 0;
            }

            foreach ($routes as  $route){
                foreach ($headers as $header){
                    $value = $this->getRouteAttributeValue($route, $header);

                    $tableArr[$header][] = $value;
                    $tableArr['chars'][$header] = strlen($value) >  $tableArr['chars'][$header]
                        ?
                        strlen($value)
                        :
                        $tableArr['chars'][$header]
                    ;
                }
            }
            foreach ($headers as $header){
                $tableArr['chars'][$header] = strlen($header) >  $tableArr['chars'][$header]
                    ?
                    strlen($header)
                    :
                    $tableArr['chars'][$header]
                ;
            }
        }catch (\Exception $e){
            return ['status' => 'error', 'msg' => 'There was an error: '.$e->getMessage()];
        }
       return [
           'status' => 'success',
           'msg'    => ' All routes.',
           'output' => $tableArr,
           'output_type' => 'table'
           ];
    }

    public function needConformation(): bool
    {
        return false;
    }

    private function getRouteAttributeValue(Rout $rout, $attribute){
        $value = null;
        switch ($attribute){
        case 'method':
            $value = $rout->getMethod();
            break;

        case 'name':
            $value = $rout->getName();
            break;

        case 'route':
            $value = $rout->getRout();
            break;

        case 'controller':
            $value = $rout->getController();
            break;
        case 'action':
            $value = $rout->getAction();
            break;
        }
        return $value;
    }
}