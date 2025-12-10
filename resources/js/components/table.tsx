import { ChevronDown, ChevronUp, Search, X } from 'lucide-react';
import { useState } from 'react';

// Komponen Table Utama
const Table = ({
    columns = [],
    data = [],
    searchable = true,
    sortable = true,
    pagination = true,
    rowsPerPage = 10,
    onRowClick = null,
    actions = null,
    loading = false,
    emptyMessage = 'Tidak ada data',
    className = '',
}) => {
    const [searchTerm, setSearchTerm] = useState('');
    const [sortConfig, setSortConfig] = useState({
        key: null,
        direction: 'asc',
    });
    const [currentPage, setCurrentPage] = useState(1);

    // Helper function untuk get nested value
    const getNestedValue = (obj, path) => {
        return path.split('.').reduce((current, key) => current?.[key], obj);
    };

    // Filter data berdasarkan pencarian
    const filteredData =
        searchable && searchTerm
            ? data.filter((row) =>
                  columns.some((col) => {
                      const value = getNestedValue(row, col.key);
                      return value
                          ?.toString()
                          .toLowerCase()
                          .includes(searchTerm.toLowerCase());
                  }),
              )
            : data;

    // Sort data
    const sortedData =
        sortable && sortConfig.key
            ? [...filteredData].sort((a, b) => {
                  const aVal = getNestedValue(a, sortConfig.key);
                  const bVal = getNestedValue(b, sortConfig.key);

                  if (aVal < bVal)
                      return sortConfig.direction === 'asc' ? -1 : 1;
                  if (aVal > bVal)
                      return sortConfig.direction === 'asc' ? 1 : -1;
                  return 0;
              })
            : filteredData;

    // Pagination
    const totalPages = Math.ceil(sortedData.length / rowsPerPage);
    const startIndex = (currentPage - 1) * rowsPerPage;
    const paginatedData = pagination
        ? sortedData.slice(startIndex, startIndex + rowsPerPage)
        : sortedData;

    const handleSort = (key) => {
        if (!sortable) return;

        setSortConfig((prev) => ({
            key,
            direction:
                prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc',
        }));
    };

    const handlePageChange = (page) => {
        setCurrentPage(Math.max(1, Math.min(page, totalPages)));
    };

    return (
        <div
            className={`overflow-hidden bg-white shadow-sm sm:rounded-lg ${className}`}
        >
            {/* Header dengan Search */}
            {(searchable || actions) && (
                <div className="border-b border-gray-200 p-4 sm:p-6">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        {searchable && (
                            <div className="relative max-w-md flex-1">
                                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <Search className="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    type="text"
                                    placeholder="Cari..."
                                    value={searchTerm}
                                    onChange={(e) => {
                                        setSearchTerm(e.target.value);
                                        setCurrentPage(1);
                                    }}
                                    className="block w-full rounded-md border border-gray-300 bg-white py-2 pr-10 pl-10 leading-5 placeholder-gray-500 transition focus:border-indigo-500 focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:outline-none sm:text-sm"
                                />
                                {searchTerm && (
                                    <button
                                        onClick={() => setSearchTerm('')}
                                        className="absolute inset-y-0 right-0 flex items-center pr-3"
                                    >
                                        <X className="h-5 w-5 text-gray-400 hover:text-gray-600" />
                                    </button>
                                )}
                            </div>
                        )}
                        {actions && (
                            <div className="flex items-center gap-2">
                                {actions}
                            </div>
                        )}
                    </div>
                </div>
            )}

            {/* Table */}
            <div className="overflow-x-auto">
                {loading ? (
                    <div className="flex items-center justify-center py-12">
                        <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-indigo-600"></div>
                        <span className="ml-3 text-sm text-gray-600">
                            Memuat data...
                        </span>
                    </div>
                ) : paginatedData.length === 0 ? (
                    <div className="py-12 text-center">
                        <svg
                            className="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                            />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">
                            {emptyMessage}
                        </h3>
                        <p className="mt-1 text-sm text-gray-500">
                            Coba ubah filter pencarian Anda.
                        </p>
                    </div>
                ) : (
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                {columns.map((column) => (
                                    <th
                                        key={column.key}
                                        onClick={() =>
                                            column.sortable !== false &&
                                            handleSort(column.key)
                                        }
                                        className={`px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase ${
                                            column.sortable !== false &&
                                            sortable
                                                ? 'cursor-pointer select-none hover:bg-gray-100'
                                                : ''
                                        }`}
                                    >
                                        <div className="flex items-center space-x-1">
                                            <span>{column.label}</span>
                                            {sortable &&
                                                column.sortable !== false && (
                                                    <span className="flex flex-col">
                                                        <ChevronUp
                                                            className={`-mb-1 h-3 w-3 ${
                                                                sortConfig.key ===
                                                                    column.key &&
                                                                sortConfig.direction ===
                                                                    'asc'
                                                                    ? 'text-indigo-600'
                                                                    : 'text-gray-400'
                                                            }`}
                                                        />
                                                        <ChevronDown
                                                            className={`h-3 w-3 ${
                                                                sortConfig.key ===
                                                                    column.key &&
                                                                sortConfig.direction ===
                                                                    'desc'
                                                                    ? 'text-indigo-600'
                                                                    : 'text-gray-400'
                                                            }`}
                                                        />
                                                    </span>
                                                )}
                                        </div>
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200 bg-white">
                            {paginatedData.map((row, rowIndex) => (
                                <tr
                                    key={rowIndex}
                                    onClick={() => onRowClick?.(row)}
                                    className={`${onRowClick ? 'cursor-pointer hover:bg-gray-50' : ''} transition-colors`}
                                >
                                    {columns.map((column) => (
                                        <td
                                            key={column.key}
                                            className="px-6 py-4 text-sm whitespace-nowrap text-gray-900"
                                        >
                                            {column.render
                                                ? column.render(
                                                      getNestedValue(
                                                          row,
                                                          column.key,
                                                      ),
                                                      row,
                                                      startIndex + rowIndex + 1,
                                                  )
                                                : getNestedValue(
                                                      row,
                                                      column.key,
                                                  )}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>

            {/* Pagination */}
            {pagination && !loading && paginatedData.length > 0 && (
                <div className="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <div className="text-sm text-gray-700">
                            Menampilkan{' '}
                            <span className="font-medium">
                                {startIndex + 1}
                            </span>{' '}
                            sampai{' '}
                            <span className="font-medium">
                                {Math.min(
                                    startIndex + rowsPerPage,
                                    sortedData.length,
                                )}
                            </span>{' '}
                            dari{' '}
                            <span className="font-medium">
                                {sortedData.length}
                            </span>{' '}
                            hasil
                        </div>

                        <div className="flex items-center space-x-2">
                            <button
                                onClick={() =>
                                    handlePageChange(currentPage - 1)
                                }
                                disabled={currentPage === 1}
                                className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Sebelumnya
                            </button>

                            <div className="hidden space-x-1 sm:flex">
                                {[...Array(Math.min(5, totalPages))].map(
                                    (_, i) => {
                                        let pageNum;
                                        if (totalPages <= 5) {
                                            pageNum = i + 1;
                                        } else if (currentPage <= 3) {
                                            pageNum = i + 1;
                                        } else if (
                                            currentPage >=
                                            totalPages - 2
                                        ) {
                                            pageNum = totalPages - 4 + i;
                                        } else {
                                            pageNum = currentPage - 2 + i;
                                        }

                                        return (
                                            <button
                                                key={i}
                                                onClick={() =>
                                                    handlePageChange(pageNum)
                                                }
                                                className={`relative inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium transition-colors ${
                                                    currentPage === pageNum
                                                        ? 'z-10 border-indigo-500 bg-indigo-50 text-indigo-600'
                                                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                                                }`}
                                            >
                                                {pageNum}
                                            </button>
                                        );
                                    },
                                )}
                            </div>

                            <button
                                onClick={() =>
                                    handlePageChange(currentPage + 1)
                                }
                                disabled={currentPage === totalPages}
                                className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Table;
