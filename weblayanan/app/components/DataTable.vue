<script lang="ts">
import { z } from "zod";

export const schema = z.object({
  id: z.number(),
  nama_layanan: z.string(),
  slug: z.string(),
  deskripsi: z.string(),
  persyaratan: z.array(z.string()),
});
</script>

<script setup lang="ts">
import type { RowSelectionState } from "@tanstack/vue-table";
import {
  IconChevronDown,
  IconChevronLeft,
  IconChevronRight,
  IconChevronsLeft,
  IconChevronsRight,
  IconFileText,
  IconLayoutColumns,
  IconSend,
} from "@tabler/icons-vue";
import { createColumnHelper, FlexRender, useTable } from "@tanstack/vue-table";

import { Badge } from "~/components/ui/badge";
import { Button } from "~/components/ui/button";
import { Checkbox } from "~/components/ui/checkbox";
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from "~/components/ui/dropdown-menu";
import { Label } from "~/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "~/components/ui/table";
import { features } from "./features";

export interface LayananItem {
  id: number;
  nama_layanan: string;
  slug: string;
  deskripsi: string;
  persyaratan: string[];
}

const props = defineProps<{
  data: LayananItem[];
}>();

const columnHelper = createColumnHelper<typeof features, LayananItem>();

const columns = columnHelper.columns([
  columnHelper.display({
    id: "select",
    header: ({ table }) =>
      h(Checkbox, {
        modelValue:
          table.getIsAllPageRowsSelected() ||
          (table.getIsSomePageRowsSelected() && "indeterminate"),
        "onUpdate:modelValue": (value: any) =>
          table.toggleAllPageRowsSelected(!!value),
        "aria-label": "Pilih semua",
      }),
    cell: ({ row }) =>
      h(Checkbox, {
        modelValue: row.getIsSelected(),
        "onUpdate:modelValue": (value: any) => row.toggleSelected(!!value),
        "aria-label": "Pilih baris",
      }),
    enableSorting: false,
    enableHiding: false,
  }),
  columnHelper.accessor("nama_layanan", {
    header: "Nama Layanan",
    cell: ({ row }) =>
      h(
        "div",
        { class: "font-semibold text-foreground flex items-center gap-2" },
        [
          h(IconFileText, { class: "size-4 text-primary shrink-0" }),
          String(row.getValue("nama_layanan")),
        ],
      ),
    enableHiding: false,
  }),
  columnHelper.accessor("deskripsi", {
    header: "Deskripsi",
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-muted-foreground text-sm line-clamp-2 max-w-md" },
        String(row.getValue("deskripsi")),
      ),
  }),
  columnHelper.accessor("persyaratan", {
    header: "Persyaratan",
    cell: ({ row }) => {
      const list = (row.getValue("persyaratan") || []) as string[];
      return h(
        "div",
        { class: "flex flex-wrap gap-1" },
        list
          .slice(0, 2)
          .map((req) =>
            h(Badge, { variant: "secondary", class: "text-xs" }, () => req),
          ),
      );
    },
  }),
  columnHelper.display({
    id: "actions",
    header: "Aksi",
    cell: ({ row }) => {
      const layanan = row.original;
      return h(
        Button,
        {
          size: "sm",
          asChild: true,
          class: "gap-1.5",
        },
        () =>
          h(
            resolveComponent("NuxtLink"),
            { to: `/pengajuan?layanan_id=${layanan.id}` },
            () => [h(IconSend, { class: "size-3.5" }), "Ajukan"],
          ),
      );
    },
  }),
]);

const rowSelection = ref<RowSelectionState>({});

const table = useTable({
  features,
  get data() {
    return props.data || [];
  },
  columns,
  state: {
    get rowSelection() {
      return rowSelection.value;
    },
  },
  onRowSelectionChange: (updater) => {
    rowSelection.value =
      typeof updater === "function" ? updater(rowSelection.value) : updater;
  },
});
</script>

<template>
  <div class="w-full flex flex-col gap-4">
    <!-- Toolbar Atas -->
    <div class="flex items-center justify-between px-4 lg:px-6">
      <h2 class="text-lg font-semibold tracking-tight">
        Daftar Layanan Administrasi
      </h2>
      <div class="flex items-center gap-2">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm">
              <IconLayoutColumns class="size-4 mr-1.5" />
              <span class="hidden lg:inline">Sesuaikan Kolom</span>
              <span class="lg:hidden">Kolom</span>
              <IconChevronDown class="size-4 ml-1" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" class="w-56">
            <template
              v-for="column in table
                .getAllColumns()
                .filter(
                  (col) =>
                    typeof col.accessorFn !== 'undefined' && col.getCanHide(),
                )"
              :key="column.id"
            >
              <DropdownMenuCheckboxItem
                class="capitalize"
                :model-value="column.getIsVisible()"
                @update:model-value="
                  (value: any) => column.toggleVisibility(!!value)
                "
              >
                {{ column.id }}
              </DropdownMenuCheckboxItem>
            </template>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>

    <!-- Tabel Utama -->
    <div class="px-4 lg:px-6">
      <div class="overflow-hidden rounded-lg border bg-card">
        <Table>
          <TableHeader class="bg-muted/50">
            <TableRow
              v-for="headerGroup in table.getHeaderGroups()"
              :key="headerGroup.id"
            >
              <TableHead
                v-for="header in headerGroup.headers"
                :key="header.id"
                :colspan="header.colSpan"
              >
                <FlexRender v-if="!header.isPlaceholder" :header="header" />
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="table.getRowModel().rows.length">
              <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :cell="cell" />
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell
                :colspan="columns.length"
                class="h-32 text-center text-muted-foreground"
              >
                Layanan tidak ditemukan. Silakan coba kata kunci lain.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>

    <!-- Pagination Footer -->
    <div class="flex items-center justify-between px-4 lg:px-6 text-sm">
      <div class="text-muted-foreground hidden flex-1 lg:flex">
        {{ table.getFilteredSelectedRowModel().rows.length }} dari
        {{ table.getFilteredRowModel().rows.length }} baris dipilih.
      </div>
      <div class="flex w-full items-center gap-6 lg:w-fit ml-auto">
        <div class="hidden items-center gap-2 lg:flex">
          <Label for="rows-per-page" class="text-xs text-muted-foreground">
            Baris per halaman
          </Label>
          <Select
            :model-value="`${table.atoms.pagination.get().pageSize}`"
            @update:model-value="(val: any) => table.setPageSize(Number(val))"
          >
            <SelectTrigger id="rows-per-page" size="sm" class="w-16 h-8">
              <SelectValue
                :placeholder="`${table.atoms.pagination.get().pageSize}`"
              />
            </SelectTrigger>
            <SelectContent side="top">
              <SelectItem
                v-for="size in [10, 20, 30]"
                :key="size"
                :value="`${size}`"
              >
                {{ size }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div class="text-xs font-medium">
          Halaman {{ table.atoms.pagination.get().pageIndex + 1 }} dari
          {{ table.getPageCount() || 1 }}
        </div>
        <div class="flex items-center gap-1">
          <Button
            variant="outline"
            size="icon"
            class="size-8"
            :disabled="!table.getCanPreviousPage()"
            @click="table.setPageIndex(0)"
          >
            <IconChevronsLeft class="size-4" />
          </Button>
          <Button
            variant="outline"
            size="icon"
            class="size-8"
            :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()"
          >
            <IconChevronLeft class="size-4" />
          </Button>
          <Button
            variant="outline"
            size="icon"
            class="size-8"
            :disabled="!table.getCanNextPage()"
            @click="table.nextPage()"
          >
            <IconChevronRight class="size-4" />
          </Button>
          <Button
            variant="outline"
            size="icon"
            class="size-8"
            :disabled="!table.getCanNextPage()"
            @click="table.setPageIndex(table.getPageCount() - 1)"
          >
            <IconChevronsRight class="size-4" />
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
