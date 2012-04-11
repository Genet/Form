<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Csrf\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Csrf\EventListener\CsrfValidationListener;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class CsrfType extends AbstractType
{
    private $csrfProvider;

    /**
     * Constructor.
     *
     * @param CsrfProviderInterface $csrfProvider The provider to use to generate the token
     */
    public function __construct(CsrfProviderInterface $csrfProvider)
    {
        $this->csrfProvider = $csrfProvider;
    }

    /**
     * Builds the CSRF field.
     *
     * A validator is added to check the token value when the CSRF field is added to
     * a root form
     *
     * @param FormBuilder $builder The form builder
     * @param array       $options The options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $csrfProvider = $options['csrf_provider'];
        $intention = $options['intention'];

        $builder
            ->setData($csrfProvider->generateCsrfToken($intention))
            ->addEventSubscriber(new CsrfValidationListener($csrfProvider, $intention))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'csrf_provider' => $this->csrfProvider,
            'intention'     => null,
            'property_path' => false,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(array $options)
    {
        return 'hidden';
    }

    /**
     * Returns the name of this form.
     *
     * @return string 'csrf'
     */
    public function getName()
    {
        return 'csrf';
    }
}
