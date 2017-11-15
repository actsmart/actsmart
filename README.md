# actsmart

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


## About

actSmart is an agent construction toolkit. It is an opinionated way of building agents
with a focus on conversational agents (chatbots). It is the framework we use internally at [GreenShoot Labs][link-greenshoot].

Our initial focus is Slack and connecting to a number of  cognitive services that we use at GreenShoot Labs. 

It is very early days so much is yet to be defined. 

### Agents

An agent for actSmart is something that can perceive its environment through sensors and can change its
environment through actuators in an effort to achieve its goals and satisfy its motivations. Read [this][medium-agents] for
a light-weight intro to the ideas.
 
From a web service point of view sensors receive information (e.g. messages from
Slack) while actuators purposefully connect with the outside word to change its state (by asking for things to
be created, updated or deleted) or to pro-actively collect information that the agent does not have. 

Agents attempt to achieve goals by executing an actions or a series of actions. For example,
a goal might be to mark the state of a task as done. The action to achieve this would be to connect with the API
that manages tasks and request that the state of a particular task is updated to done. 

As the framework evolves concepts will be more explicitly expressed (e.g. we do not currently explicitly represent goals). 
Did I mention it's early days.

### Conversations

actSmart uses a graph structure to provide templates of potential conversations between a user and the agent. A
conversation is divided in scenes. Participants in a scene exchange utterances that will either complete the conversation
or resolve the purpose of that specific scene and advance the conversation to the next scene. Scenes have preconditions
and postconditions as do specific utterances within a scene. Furthermore, utterances can cause actions (as described above)
to take place. 

Consider, for example, a simple pizza ordering scenario. The user might start with: 

"-Can I order some pizzas please?"

When actSmart receives that utterance it searches for a conversation template with an opening scene that matches that and 
sets that conversation as the active conversation. It then identifies how it should reply. For example:  

"-How many pizzas would you like to order?"

The user replies:

"-2 pizzas please."

This user utterance completes the initial scene and move us on to a new scene where we know we need
to collect the details on the pizzas (i.e. the purpose of the scene is to find out exactly what 2 pizzas the user wants
and the precondition is that the user has already defined how many pizzas they need). This second scene could evolve as follows:

"-What should the first pizza be?"
"-Margherita"
"-And what should the second pizza be?"
"-Pinneaple and ham"

and with that we complete the pizzas selection scene and can proceed to a drink ordering scene, side-orders, etc.

We will be posting actual examples once the scene structure is stable enough for it to make sense! 

 
## Install

Via Composer

``` bash
$ composer require actsmart/actsmart
```

## Usage

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email ronald@greenshootlabs.com instead of using the issue tracker.

## Credits

- [GreenShoot Labs][link-greenshoot]
- [Ronald Ashri][link-ronald]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/actsmart/actsmart.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/actsmart/actsmart/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/actsmart/actsmart.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/actsmart/actsmart.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/actsmart/actsmart
[link-travis]: https://travis-ci.org/actsmart/actsmart
[link-code-quality]: https://scrutinizer-ci.com/g/actsmart/actsmart
[link-downloads]: https://packagist.org/packages/actsmart/actsmart
[link-greenshoot]: https://greenshootlabs.com
[link-ronald]: https://twitter.com/ronald_istos
[medium-agents]: https://hackernoon.com/making-artificial-intelligence-work-for-you-part-1-what-is-ai-dd7512058e0e
