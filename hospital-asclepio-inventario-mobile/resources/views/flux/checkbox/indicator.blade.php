@blaze(fold: true)

@php
$classes = Flux::classes()
    ->add('shrink-0 size-5 rounded-md flex justify-center items-center transition-all cursor-pointer')
    ->add('text-sm text-white font-bold')
    ->add('shadow-xs [ui-checkbox[disabled]_&]:opacity-75 [ui-checkbox[data-checked][disabled]_&]:opacity-50')
    ->add('[ui-checkbox[data-checked]:not([data-indeterminate])_&>svg:first-child]:block [ui-checkbox[data-indeterminate]_&>svg:last-child]:block')
    ->add('[ui-checkbox[aria-checked="true"]:not([data-indeterminate])_&>svg:first-child]:block')
    ->add('[ui-checkbox[checked]:not([data-indeterminate])_&>svg:first-child]:block')
    ->add([
        'border-2',
        'border-slate-400 dark:border-slate-500',
        'hover:border-emerald-600 dark:hover:border-emerald-400',
        '[ui-checkbox[data-checked]_&]:border-emerald-600 dark:[ui-checkbox[data-checked]_&]:border-emerald-500',
        '[ui-checkbox[aria-checked="true"]_&]:border-emerald-600 dark:[ui-checkbox[aria-checked="true"]_&]:border-emerald-500',
        '[ui-checkbox[checked]_&]:border-emerald-600 dark:[ui-checkbox[checked]_&]:border-emerald-500',
        '[print-color-adjust:exact]',
    ])
    ->add([
        'bg-white dark:bg-slate-800',
        'hover:bg-emerald-50 dark:hover:bg-slate-700',
        '[ui-checkbox[data-checked]_&]:bg-emerald-600 dark:[ui-checkbox[data-checked]_&]:bg-emerald-500',
        '[ui-checkbox[aria-checked="true"]_&]:bg-emerald-600 dark:[ui-checkbox[aria-checked="true"]_&]:bg-emerald-500',
        '[ui-checkbox[checked]_&]:bg-emerald-600 dark:[ui-checkbox[checked]_&]:bg-emerald-500',
        'hover:[ui-checkbox[data-checked]_&]:bg-emerald-700 dark:hover:[ui-checkbox[data-checked]_&]:bg-emerald-400',
    ])
    ;
@endphp

<div {{ $attributes->class($classes) }} data-flux-checkbox-indicator>
    <flux:icon.check variant="micro" class="hidden text-white font-bold stroke-[3]" />
    <flux:icon.minus variant="micro" class="hidden text-white font-bold stroke-[3]" />
</div>


