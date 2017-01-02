<?php

    use Masterkey\Repository\BaseRepository;


    class UserRepository extends BaseRepository
    {
        public function __construct($app, $collection)
        {
            parent::__construct($app, $collection);
        }

        public function model()
        {
            // Cria um esboço para a classe AlgumaClasse.
            return Mockery::mock('Illuminate\Database\Eloquent\Model');
        }
    }
