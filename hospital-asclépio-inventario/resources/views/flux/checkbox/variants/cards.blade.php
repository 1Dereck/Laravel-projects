@blaze(fold: true, unsafe: ['icon:variant'])

@php $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware([ 'indicator' ])

@props([
    'iconVariant' => 'micro',
    'description' => null,
    'indicator' => true,
    'accent' => true,
    'label' => null,
    'icon' => null,
])

@php

$iconClasses = Flux::classes()
    ->add('inline-block mt-0.5 text-slate-500 dark:text-slate-400 [ui-checkbox[data-checked]_&]:text-emerald-600 dark:[ui-checkbox[data-checked]_&]:text-emerald-400')
    // When using the outline icon variant, we need to size it down to match the default icon sizes...
    ->add($iconVariant === 'outline' ? 'size-4' : '')
    ;

$classes = Flux::classes()
    ->add('relative flex justify-between items-center gap-3 flex-1 p-3.5 cursor-pointer select-none transition-all')
    ->add('rounded-xl shadow-xs')
    ->add('bg-slate-50 dark:bg-slate-900/90 hover:bg-slate-100/80 dark:hover:bg-slate-800/80')
    ->add('border border-slate-300 dark:border-slate-800')
    ->add('data-checked:bg-emerald-500/10 dark:data-checked:bg-emerald-500/15 data-checked:border-emerald-500 dark:data-checked:border-emerald-500/80')
    ->add('[&[disabled]]:opacity-50 dark:[&[disabled]]:opacity-75 [&[disabled]]:cursor-default [&[disabled]]:pointer-events-none')
    ;
@endphp

{{-- We have to put tabindex="-1" here because otherwise, Livewire requests will wipe out tabindex state, --}}
{{-- even with durable attributes for some reason... --}}
{{-- We have to put "data-flux-field" so that a single box can be disabled without "disabling" the group field label... --}}
<ui-checkbox {{ $attributes->class($classes) }} data-flux-control data-flux-checkbox-cards tabindex="-1" data-flux-field>
    <?php if ($label): ?>
        <div class="flex-1 flex gap-2">
            <?php if (is_string($icon) && $icon !== ''): ?>
                <flux:icon :icon="$icon" :variant="$iconVariant" :class="$iconClasses" />
            <?php elseif ($icon): ?>
                {{ $icon }}
            <?php endif; ?>

            <div class="flex-1">
                <flux:heading>{{ $slot->isNotEmpty() ? $slot : $label }}</flux:heading>

                <?php if ($description): ?>
                    <flux:subheading size="sm">{{ $description }}</flux:subheading>
                <?php endif; ?>
            </div>
        </div>

        <flux:checkbox.indicator />
    <?php else: ?>
        {{ $slot }}
    <?php endif; ?>
</ui-checkbox>
