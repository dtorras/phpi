<?php

/**
 * This example makes use of the two onboard PWM outputs, pulsing inverted duty cycles to two LEDs.
 *
 * Requirements:
 * LED anode connected to BCM pin 18
 * LED anode connected to BCM pin 19
 *
 */


include __DIR__ . '/../vendor/autoload.php';

use Calcinai\PHPi\Pin\PinFunction;


$loop = \React\EventLoop\Factory::create();
$board = \Calcinai\PHPi\Factory::create($loop);

$board->getPin(18)->setFunction(PinFunction::PWM0);
$board->getPin(19)->setFunction(PinFunction::PWM1);

$pwm0 = $board->getPWM(\Calcinai\PHPi\Peripheral\PWM::PWM0)
    ->start();

$pwm1 = $board->getPWM(\Calcinai\PHPi\Peripheral\PWM::PWM1)
    ->start();

$board->getPin(23)->setFunction(PinFunction::OUTPUT)->low();

/**
 * Everything following is mainly outside the scope of the PWM, it just makes a nice level animation on the output.
 *
 * Not coded for efficiency either!
 */


$loop->addPeriodicTimer($update_interval = 0.01, function () use ($pwm0, $pwm1) {

    $max = 50;

    $total_time_ms = 50;
    //Simple sine easing function to calculate the duty
    $pc = -($max / 2) * (cos(M_PI * abs($total_time_ms / 2 - (microtime(true) * 1000) % $total_time_ms) / ($total_time_ms / 2)) - 1);

//    $pc = abs((microtime(true) * 1000) % $total_time_ms) / $total_time_ms * $max;

    /** This is the only line relevant to the actual PWM! */
    $pwm0->setDutyCycle($pc);
    $pwm1->setDutyCycle($max - $pc); //Opposite duty
});

$loop->run();
