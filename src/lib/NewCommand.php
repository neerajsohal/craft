<?php namespace Laravel\Craft;

use ZipArchive;
use Guzzle\Http\Client as HttpClient;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class NewCommand extends BaseCommand {

	/**
	 * Configure the console command.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('new')
			 ->setDescription('Create a new Laravel application')
			 ->addArgument('name', InputArgument::REQUIRED, 'The name of the application')
			 ->addOption('--dir', '-d', InputOption::VALUE_OPTIONAL, 'Target directory to install new application in');
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//$directory = getcwd().'/'.$input->getArgument('name');
		$path = $input->getOption('dir');
		if($path) {
			$directory = getcwd() . '/' . $input->getOption('dir') .'/'.$input->getArgument('name');
		} else {
			$directory = getcwd() . '/' . $input->getArgument('name');
		}

		if (is_dir($directory))
		{
			$output->writeln('<error>Application already exists!</error>'); exit(1);
		}

		$output->writeln('<info>Crafting application...</info>');

		// Creaqte the ZIP file name...
		$zipFile = getcwd().'/laravel_'.md5(time().uniqid()).'.zip';

		// Download the latest Laravel archive...
		$client = new HttpClient;
		$client->get('http://192.241.224.13/laravel-craft.zip')->setResponseBody($zipFile)->send();

		// Create the application directory...
		mkdir($directory);

		// Unzip the Laravel archive into the application directory...
		$archive = new ZipArchive;
		$archive->open($zipFile);
		$archive->extractTo($directory);
		$archive->close();

		// Delete the Laravel archive...
		@chmod($zipFile, 0777);
		@unlink($zipFile);

		$output->writeln('<comment>Application ready! Build something amazing.</comment>');
	}

}