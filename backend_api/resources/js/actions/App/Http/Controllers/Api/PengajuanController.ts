import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PengajuanController::store
 * @see app/Http/Controllers/Api/PengajuanController.php:59
 * @route '/api/pengajuan'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/pengajuan',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PengajuanController::store
 * @see app/Http/Controllers/Api/PengajuanController.php:59
 * @route '/api/pengajuan'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PengajuanController::store
 * @see app/Http/Controllers/Api/PengajuanController.php:59
 * @route '/api/pengajuan'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\PengajuanController::store
 * @see app/Http/Controllers/Api/PengajuanController.php:59
 * @route '/api/pengajuan'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\PengajuanController::store
 * @see app/Http/Controllers/Api/PengajuanController.php:59
 * @route '/api/pengajuan'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/pengajuan/riwayat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\PengajuanController::index
 * @see app/Http/Controllers/Api/PengajuanController.php:18
 * @route '/api/pengajuan/riwayat'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/pengajuan/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
    const statsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: stats.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
        statsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: stats.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\PengajuanController::stats
 * @see app/Http/Controllers/Api/PengajuanController.php:187
 * @route '/api/pengajuan/stats'
 */
        statsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: stats.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    stats.form = statsForm
/**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
export const lacak = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lacak.url(args, options),
    method: 'get',
})

lacak.definition = {
    methods: ["get","head"],
    url: '/api/pengajuan/lacak/{nomor_pelacakan}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
lacak.url = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { nomor_pelacakan: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    nomor_pelacakan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        nomor_pelacakan: args.nomor_pelacakan,
                }

    return lacak.definition.url
            .replace('{nomor_pelacakan}', parsedArgs.nomor_pelacakan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
lacak.get = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lacak.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
lacak.head = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: lacak.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
    const lacakForm = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: lacak.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
        lacakForm.get = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: lacak.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\PengajuanController::lacak
 * @see app/Http/Controllers/Api/PengajuanController.php:129
 * @route '/api/pengajuan/lacak/{nomor_pelacakan}'
 */
        lacakForm.head = (args: { nomor_pelacakan: string | number } | [nomor_pelacakan: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: lacak.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    lacak.form = lacakForm
/**
* @see \App\Http\Controllers\Api\PengajuanController::updateStatus
 * @see app/Http/Controllers/Api/PengajuanController.php:153
 * @route '/api/pengajuan/{id}/status'
 */
export const updateStatus = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

updateStatus.definition = {
    methods: ["put"],
    url: '/api/pengajuan/{id}/status',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\PengajuanController::updateStatus
 * @see app/Http/Controllers/Api/PengajuanController.php:153
 * @route '/api/pengajuan/{id}/status'
 */
updateStatus.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return updateStatus.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PengajuanController::updateStatus
 * @see app/Http/Controllers/Api/PengajuanController.php:153
 * @route '/api/pengajuan/{id}/status'
 */
updateStatus.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\PengajuanController::updateStatus
 * @see app/Http/Controllers/Api/PengajuanController.php:153
 * @route '/api/pengajuan/{id}/status'
 */
    const updateStatusForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\PengajuanController::updateStatus
 * @see app/Http/Controllers/Api/PengajuanController.php:153
 * @route '/api/pengajuan/{id}/status'
 */
        updateStatusForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateStatus.form = updateStatusForm
const PengajuanController = { store, index, stats, lacak, updateStatus }

export default PengajuanController