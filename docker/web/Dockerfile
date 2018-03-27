FROM hashicorp/envconsul:alpine AS envconsul

FROM httpd:alpine

COPY --from=envconsul /bin/envconsul /bin/envconsul

COPY conf/httpd.conf $HTTPD_PREFIX/conf
