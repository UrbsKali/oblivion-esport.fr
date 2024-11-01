import Image from './components/markdown/Image.svelte';
import Heading from './components/markdown/Heading.svelte';
import List from './components/markdown/List.svelte';
import Table from './components/markdown/Table.svelte';
import TableCell from './components/markdown/TableCell.svelte';
import Paragraph from './components/markdown/Paragraph.svelte';

export const renderers = {
    image: Image,
    heading: Heading,
    list: List,
    tableCell: TableCell,
    table: Table,
    paragraph: Paragraph
};