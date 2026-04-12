import { defineCollection } from 'astro:content'
import { z } from 'astro/zod'
import { glob } from 'astro/loaders'

const docs = defineCollection({
  loader: glob({ pattern: '**/*.{md,mdx}', base: './src/content/docs' }),
  schema: z
    .object({
      title: z.string(),
      description: z.string().optional(),
      sidebar: z
        .object({
          label: z.string().optional(),
          order: z.number().optional(),
        })
        .optional(),
    })
    .passthrough(),
})

export const collections = { docs }
