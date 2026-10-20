<script setup lang="ts">
import type { Component, HTMLAttributes } from "vue"
import type { ButtonVariants } from "."
import { cn } from "cn"
import { Primitive } from "reka-ui"
import { buttonVariants } from "."

interface Props {
  variant?: ButtonVariants["variant"]
  size?: ButtonVariants["size"]
  class?: HTMLAttributes["class"]
  as?: string | Component
  asChild?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  as: "button",
  asChild: false,
  disabled: false,
})
</script>

<template>
  <Primitive
    data-slot="button"
    :data-variant="props.variant"
    :data-size="props.size"
    :as="props.as"
    :as-child="props.asChild"
    :disabled="props.disabled"
    :class="cn(buttonVariants({ variant: props.variant, size: props.size }), props.class)"
  >
    <slot />
  </Primitive>
</template>
